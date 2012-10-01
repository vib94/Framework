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

	const CLEAR_URL_TAG = 'clear';

	private $_statement = null;
	
	private $_prefix = null;

	private $_session = null;
	
	private $_request = null;
	private $_request_params = 0;
	
	private $_multi_sort = false;

	private $_first_page = 1;

	private $_current_page = null;
	private $_last_page = null;
	private $_per_page = null;

	private $_order_by = array();
	
	private $_total = null;
	
	public function __construct(Yab_Db_Statement $statement, $prefix = null, $request_params = 0, $multi_sort = true) {

		$this->_statement = $statement;
		
		$this->_prefix = (string) $prefix;
		$this->_request_params = (int) $request_params;	
		$this->_multi_sort = (bool) $multi_sort;
		
		$this->_request = Yab_Loader::getInstance()->getRequest();

		if($this->_prefix) {
		
			$session = Yab_Loader::getInstance()->getSession();

			if(!$session->has('pager_'.$this->_prefix))
				$session->set('pager_'.$this->_prefix, array());

			$this->_session = $session->cast('pager_'.$this->_prefix);
		
		} else {
		
			$this->_session = new Yab_Object();
		
		}
		
		if($this->_request->getParam($this->_request_params) == self::CLEAR_URL_TAG)
			$this->_session->clear();
		
		$this->_order_by = $this->_statement->getOrderBy();
		
	}
	
	public function getFilteredStatement() {
	
		$statement = clone $this->_statement;
		
		$statement->free();
		
		$tables = $statement->getTables();

		foreach($this->_request->getGet() as $key => $value) {
		
			if(!preg_match('#^filter_([^~]+)~([^:]+)$#i', $key, $match))
				continue;
				
			$this->_session->set($match[0], $value);
		
		}

		foreach($this->_session as $key => $value) {
		
			if(!preg_match('#^filter_([^~]+)~([^~]+)$#i', $key, $match))
				continue;
			
			$table_alias = $match[1];
			$table_column = $match[2];
			
			if(!array_key_exists($table_alias, $tables))
				continue;
			
			$table_name = $tables[$table_alias];
			
			$columns = $statement->getAdapter()->getColumns($table_name);
			
			if(!array_key_exists($table_column, $columns))
				continue;
			
			$column = $columns[$table_column];
			
			if(is_array($value)) {
			
				$statement->where($statement->getAdapter()->quoteIdentifier($table_alias).'.'.$statement->getAdapter()->quoteIdentifier($table_column).' IN ('.implode(', ', array_map(array($statement->getAdapter(), 'quote'), $value)).')');

			} else {

				$statement->where($statement->getAdapter()->quoteIdentifier($table_alias).'.'.$statement->getAdapter()->quoteIdentifier($table_column).' LIKE '.$statement->getAdapter()->quote('%'.$value.'%'));

			}
		
		}
		
		return $statement;
	
	}

	public function getStatement($sql_limit = true) {
	
		$statement = $this->getFilteredStatement();
		
		$order_by = $this->getSqlOrderBy();
		
		if(count($order_by))
			$statement->orderBy($order_by);

		if($sql_limit)
			return $statement->sqlLimit(($this->getCurrentPage() - 1) * $this->getPerPage(), $this->getPerPage());		

		$this->_total = count($statement->query());
	
		return $statement->limit(($this->getCurrentPage() - 1) * $this->getPerPage(), $this->getPerPage());		

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
		
			array_push($params, self::CLEAR_URL_TAG);
		
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
			$this->_per_page = 25;

		$this->_per_page = max(1, intval($this->_per_page));

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

			if($column && $this->_sanitizeColumn($column))
				$order_by[$column] = strtolower($order) == 'desc' ? 'desc' : 'asc';

			if(!$this->_multi_sort)
				break;

			$i = $i + 2;

		}

		if($additionnal_column && $this->_sanitizeColumn($additionnal_column)) {

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
		
			$sort = $this->_sanitizeColumn($sort);
			
			if(!$sort)
				continue;
				
			$sanitize_order_by[$sort] = $order;
	
		}
		
		return $sanitize_order_by;

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

	private function _sanitizeColumn($column) {

		if(!$column)
			return '';
		
		if(!preg_match('#^\s*SELECT(\s+.+\s+)FROM#is', $this->_statement->getPackedSql(), $match))
			return '';

		if(!preg_match('#([a-z0-9\._]*'.preg_quote($column, '#').')([^a-z0-9\._]|$)#uis', $match[1], $match))
			return '';

		return $match[1];
	
	}

}

// Do not clause PHP tags unless it is really necessary