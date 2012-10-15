<?php
/**
 * Yab Framework
 *  
 * @category   Yab_Helper
 * @package    Yab_Helper_Pager
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Helper_Pager {

	private $_statement = null;
	
	private $_prefix = null;

	private $_session = null;
	
	private $_request = null;
	private $_request_params = 0;
	
	private $_multi_sort = true;

	private $_first_page = 1;

	private $_current_page = null;
	private $_last_page = null;
	private $_per_page = null;
	private $_default_per_page = 25;
	private $_max_per_page = null;

	private $_order_by = array();
	
	private $_total = null;
	
	private $_clear_url_tag = 'clear';
	private $_export_url_tag = 'export';
	private $_filter_prefix = 'filter_';
	private $_filter_separator = '~';

	public function __construct(Yab_Db_Statement $statement, $prefix = null, $request_params = 0) {

		$this->_statement = $statement;
		
		$this->_prefix = (string) $prefix;
		$this->_request_params = (int) $request_params;	

		$this->_request = Yab_Loader::getInstance()->getRequest();

		if($this->_prefix) {
		
			$session = Yab_Loader::getInstance()->getSession();

			if(!$session->has($this->_prefix))
				$session->set($this->_prefix, array());

			$this->_session = $session->cast($this->_prefix);
		
		} else {
		
			$this->_session = new Yab_Object();
		
		}
		
		if($this->_request->getParam($this->_request_params) == $this->_clear_url_tag)
			$this->_session->clear();
		
		$this->_order_by = $this->_statement->getOrderBy();
		
	}

	public function getFilteredStatement() {
	
		$adapter = $this->_statement->getAdapter();
	
		$statement = clone $this->_statement;
		
		$statement->free();
		
		$tables = $statement->getTables();

		$filter_regexp = '#^'.preg_quote($this->_filter_prefix, '#').'([^'.preg_quote($this->_filter_separator, '#').']+)'.preg_quote($this->_filter_separator, '#').'([^'.preg_quote($this->_filter_separator, '#').']+)$#i';
		
		foreach($this->_request->getRequest() as $key => $value) {
		
			if(!preg_match($filter_regexp, $key, $match))
				continue;
			
			$this->_session->set($match[0], $value);
		
		}

		foreach($this->_session as $key => $value) {
		
			if(!preg_match($filter_regexp, $key, $match))
				continue;
			
			$table_alias = $match[1];
			$table_column = $match[2];
			
			if(!array_key_exists($table_alias, $tables))
				continue;
			
			$table = $tables[$table_alias];
			
			$columns = $table->getColumns();
			
			if(!array_key_exists($table_column, $columns))
				continue;
			
			$column = $columns[$table_column];
			
			if(is_array($value)) {
			
				if(count($value))
					$statement->where($adapter->quoteIdentifier($table_alias).'.'.$adapter->quoteIdentifier($table_column).' IN ('.implode(', ', array_map(array($adapter, 'quote'), $value)).')');

			} else {

				if($value)
					$statement->where($adapter->quoteIdentifier($table_alias).'.'.$adapter->quoteIdentifier($table_column).' LIKE '.$adapter->quote('%'.$value.'%'));

			}
		
		}
		
		return $statement;
	
	}

	public function getStatement($sql_limit = true) {
	
		$statement = $this->getFilteredStatement();
		
		$order_by = $this->getSqlOrderBy();
		
		if(count($order_by))
			$statement->orderBy($order_by);

		$this->export();
			
		if($sql_limit)
			return $statement->sqlLimit(($this->getCurrentPage() - 1) * $this->getPerPage(), $this->getPerPage());		

		$this->_total = count($statement->query());
	
		return $statement->limit(($this->getCurrentPage() - 1) * $this->getPerPage(), $this->getPerPage());		

	}
	
	public function export() {

		if(!$this->_request->getGet()->has($this->_export_url_tag))
			return $this;
			
		$statement = $this->getFilteredStatement();
		
		$order_by = $this->getSqlOrderBy();
		
		if(count($order_by))
			$statement->orderBy($order_by);
		
		$file_name = $this->_prefix ? $this->_prefix : $this->_export_url_tag;
		$file_name .= '_'.date('Y-m-d-H-i-s');
		
		if($this->_request->getGet()->get($this->_export_url_tag) == 'csv') {

			$csv = new Yab_File_Csv($file_name.'.csv');

			$csv->setDatas($statement);

			Yab_Loader::getInstance()->getResponse()->download($csv);

		}
		
		if($this->_request->getGet()->get($this->_export_url_tag) == 'xml') {

			$xml = new Yab_File_Xml($file_name.'.xml');

			$xml->setDatas($statement);

			Yab_Loader::getInstance()->getResponse()->download($xml);

		}
		
		return $this;

	}
	
	public function getFilterStatement($table_alias, $column_key, $column_value) {
	
		$adapter = $this->_statement->getAdapter();
	
		return $this->getFilteredStatement()->select(
			'DISTINCT '.
			$adapter->quoteIdentifier($table_alias).'.'.$adapter->quoteIdentifier($column_key).', '.
			$adapter->quoteIdentifier($table_alias).'.'.$adapter->quoteIdentifier($column_value)
		)->orderBy(
			array($column_value => 'asc')
		)->setKey($column_key)->setValue($column_value);

	}	
	
	public function getFilterName($table_alias, $column_key) {
	
		return $this->_filter_prefix.$table_alias.$this->_filter_separator.$column_key;

	}
	
	public function getFilters(array $table_aliases) {

		$form = null;
		
		foreach($table_aliases as $table_alias => $columns) {
		
			if(!is_array($columns))
				$columns = array($columns);
		
			foreach($columns as $column_key => $column_value) {
			
				if(is_numeric($column_key)) {
				
					$column_key = $column_value;
					$column_value = null;
				
				}
				
				$element = $this->getFilter($table_alias, $column_key, $column_value, $form);
				
				$form = $element->getForm();

			}
		
		}

		return $form;

	}
	
	public function getFilter($table_alias, $column_key, $column_value = null, Yab_Form $form = null) {
	
		if($form === null) {
	
			$form = new Yab_Form();
			
			$form->set('method', 'get')->set('action', '');
		
		}

		$filter_name = $this->getFilterName($table_alias, $column_key);
		
		$attributes = array(
			'id' => $filter_name,
			'name' => $filter_name,
			'type' => 'text',
			'value' => $this->_session->has($filter_name) ? $this->_session->get($filter_name) : null,
		);
		
		if($column_value) {
		
			$attributes['type'] = 'select';
			$attributes['options'] = $this->getFilterStatement($table_alias, $column_key, $column_value);
			
		}
				
		$form->setElement($filter_name, $attributes);

		$element = $form->getElement($filter_name);
		
		$element->set('value', $this->_session->has($filter_name) ? $this->_session->get($filter_name) : null);
		
		return $element;

	}

	public function getPagination($wave = 5, $total = true, $reset = true) {

		$wave = (int) $wave;

		$html = '<ul class="pager">';
		
		if(1 < max(1, $this->getCurrentPage() - $wave))
			$html .= '<li><a href="'.$this->getRequest(1).'">1</a></li>';
		
		if(2 < max(1, $this->getCurrentPage() - $wave))
			$html .= '<li class="separator"><span>...</span></li>';

		for($i = max(1, $this->getCurrentPage() - $wave); $i < $this->getCurrentPage(); $i++) 
			$html .= '<li><a href="'.$this->getRequest($i).'">'.$i.'</a></li>';

		$html .= '<li class="page"><span>'.$this->getCurrentPage().'</span></li>';
		
		for($i = $this->getCurrentPage() + 1; $i <= min($this->getCurrentPage() + $wave, $this->getLastPage()); $i++)
			$html .= '<li><a href="'.$this->getRequest($i).'">'.$i.'</a></li>';

		if($this->getCurrentPage() + $wave < $this->getLastPage() - 1)
			$html .= '<li class="separator"><span>...</span></li>';
			
		if($this->getCurrentPage() + $wave < $this->getLastPage())
			$html .= '<li><a href="'.$this->getRequest($this->getLastPage()).'">'.$this->getLastPage().'</a></li>';
		
		if($total)
			$html .= '<li class="total"><span>Total :</span> <a href="'.$this->getRequest(1, $this->getTotal()).'">'.$this->getTotal().'</a></li>';
			
		if($reset)
			$html .= '<li class="reset"><a href="'.$this->getRequest().'">Reset</a></li>';

		$html .= '</ul>';
		
		return $html;

	}
	
	public function getSortLink($column, $label = null) {
	
		if($label === null)
			$label = $column;
	
		$filter_html = new Yab_Filter_Html();

		$order = $this->getColumnOrder($column);
		
		if($order == 'asc') $arrow = $this->getColumnOrderNumber($column).'&uarr;&nbsp;';
		elseif($order == 'desc') $arrow = $this->getColumnOrderNumber($column).'&darr;&nbsp;';
		else $arrow = '';

		return $arrow.'<a href="'.$this->getUrl($column).'" class="'.$order.'">'.$filter_html->filter($label).'</a>';
	
	}
	
	public function getUrl($column, $order = null) {

		return $this->getRequest($this->getCurrentPage(), $this->getPerPage(), $this->getOrderBy($column, $order));

	}

	public function getRequest($current_page = null, $per_page = null, $order_by = null) {

		$params = array_slice($this->_request->getParams(), 0, $this->_request_params);

		$clear_url = (bool) ($current_page === null && $per_page === null && $order_by === null);
		
		if($clear_url) {
		
			array_push($params, $this->_clear_url_tag);
		
		} else {
		
			if($current_page === null)
				$current_page = $this->getCurrentPage();

			if($per_page === null)
				$per_page = $this->getPerPage();
		
			array_push($params, $current_page);
			array_push($params, $per_page);	
			
			if(!is_array($order_by))
				$order_by = $this->getOrderBy();

			foreach($order_by as $column => $order)
				array_push($params, $column, $order);
			
		}

		$filter_query_string = new Yab_Filter_QueryString();

		$query_string = $filter_query_string->filter($this->_request->getGet()->toArray());

		return rtrim(Yab_Loader::getInstance()->getRequest($this->_request->getController(), $this->_request->getAction(), $params), '?').($query_string ? '?'.$query_string : '');

	}

	public function getTotal() {

		return is_numeric($this->_total) ? $this->_total : count($this->_statement);

	}

	public function getLastPage() {

		if($this->_last_page !== null)
			return $this->_last_page;

		$this->_last_page = max(1, ceil($this->getTotal() / $this->getPerPage()));

		return $this->_last_page;

	}

	public function getFirstPage() {

		return $this->_first_page;

	}

	public function getCurrentPage() {

		if($this->_current_page !== null)
			return $this->_current_page;

		$this->_current_page = $this->_getRequestParam(0);

		$this->_current_page = max(1, intval($this->_current_page));

		$this->_current_page = min($this->getLastPage(), $this->_current_page);

		return $this->_current_page;

	}

	public function getPerPage() {

		if($this->_per_page !== null)
			return $this->_per_page;

		$this->_per_page = $this->_getRequestParam(1);

		if(!$this->_per_page)
			$this->_per_page = $this->_default_per_page;

		$this->_per_page = max(1, intval($this->_per_page));

		if($this->_max_per_page)
			$this->_per_page = min($this->_max_per_page, $this->_per_page);
		
		return $this->_per_page;

	}

	public function getColumnOrder($column) {

		$order_by = $this->getOrderBy();

		if(!array_key_exists($column, $order_by))
			return '';

		return $order_by[$column];

	}

	public function getColumnOrderNumber($column) {

		$order_by = $this->getOrderBy();

		if(!array_key_exists($column, $order_by))
			return '';

		$i = 1;
			
		foreach($order_by as $col => $order) {
			
			if($col == $column)
				return $i;
				
			$i++;
		
		}
			
		return null;

	}

	public function getOrderBy($additionnal_column = null, $additionnal_order = null) {

		$i = 0;

		$order_by = array();

		while(($column = $this->_getRequestParam(2 + $i)) && ($order = $this->_getRequestParam(3 + $i))) {

			if($column && $this->_validSortColumn($column))
				$order_by[$column] = strtolower($order) == 'desc' ? 'desc' : 'asc';

			if(!$this->_multi_sort)
				break;

			$i = $i + 2;

		}

		if($additionnal_column && $this->_validSortColumn($additionnal_column)) {

			if(!$this->_multi_sort)
				$order_by = array_key_exists($additionnal_column, $order_by) ? array($additionnal_column => $order_by[$additionnal_column]) : array();

			if($additionnal_order != 'desc')
				$additionnal_order = 'asc';

			if(!array_key_exists($additionnal_column, $order_by)) {

				$order_by[$additionnal_column] = $additionnal_order;

			} elseif($order_by[$additionnal_column] == 'asc') {

				$order_by[$additionnal_column] = 'desc';

			} else {

				$order_by = array();

			}

		}

		return $order_by;

	}

	public function getClass(Yab_Db_Statement $statement, $alt = 2, $alt_class = 'alt') {
	
		$classes = array();

		array_push($classes, $alt_class.ceil($statement->offset() % intval($alt)));

		if($statement->isFirst())
			array_push($classes, 'first');

		if($statement->hasNext())
			array_push($classes, 'next');

		if($statement->isLast())
			array_push($classes, 'last');
	
		if(!count($classes))
			return '';
			
		return ' class="'.implode(' ', $classes).'"';

	}

	public function getSqlOrderBy($additionnal_column = null, $additionnal_order = null) {

		$order_by = $this->getOrderBy($additionnal_column, $additionnal_order);

		foreach($this->_order_by as $column_name => $column_order) {

			$order = true;

			foreach($order_by as $key => $value) {

				if(preg_match('#'.preg_quote($key, '#').'#is', $column_name))
					$order &= false;

			}

			if($order)
				$order_by[$column_name] = $column_order;

		}

		$sanitize_order_by = array();
		
		foreach($order_by as $sort => $order) {
		
			$sort = $this->_validSortColumn($sort);
			
			if(!$sort)
				continue;
				
			$sanitize_order_by[$sort] = $order;
	
		}
		
		return $sanitize_order_by;

	}

	public function setFilterPrefix($prefix) {
	
		$this->_filter_prefix = (string) $prefix;
		
		return $this;
	
	}

	public function setFilterSeparator($separator) {
	
		$this->_filter_separator = (string) $separator;
		
		return $this;
	
	}

	public function setClearUrlTag($tag) {
	
		$this->_clear_url_tag = (string) $tag;
		
		return $this;
	
	}

	public function setExportUrlTag($tag) {
	
		$this->_export_url_tag = (string) $tag;
		
		return $this;
	
	}

	public function setDefaultPerPage($per_page) {
		
		$this->_default_per_page = (int) $per_page;

		return $this;

	}

	public function setMaxPerPage($max_per_page) {
		
		$this->_max_per_page = (int) $max_per_page;

		return $this;

	}
	
	public function setMultiSort($multi_sort) {
		
		$this->_multi_sort = (bool) $multi_sort;

		return $this;

	}

	private function _getRequestParam($key) {

		$key = (int) $key;
		
		$key += $this->_request_params;
		
		$param = null;

		if((count($this->_request->getParams()) - $this->_request_params < 2) && $this->_session->has('param_'.$key))
			$param = $this->_session->get('param_'.$key);

		if($this->_request->getParam($key))
			$param = $this->_request->getParam($key);

		if($param !== null)
			$this->_session->set('param_'.$key, $param);

		return $param;

	}

	private function _validSortColumn($column_name) {

		if(!$column_name)
			return '';
		
		if(preg_match('#^\s*SELECT(\s+.+\s+)FROM#is', $this->_statement->getPackedSql(), $match))
			if(preg_match('#([a-z0-9\._]*'.preg_quote($column_name, '#').')([^a-z0-9\._]|$)#uis', $match[1], $match))
				return $match[1];

		if(is_numeric(strpos($column_name, '.')))
			$column_name = substr($column_name, strpos($column_name, '.') + 1, strlen($column_name) - strpos($column_name, '.'));

		foreach($this->_statement->getTables() as $alias => $table) 
			foreach($table->getColumns() as $column) 
				if($column_name == $column->getName())
					return $this->_statement->getAdapter()->quoteIdentifier($alias).'.'.$this->_statement->getAdapter()->quoteIdentifier($column_name);

		return '';
	
	}

}

// Do not clause PHP tags unless it is really necessary