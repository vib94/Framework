<?php
/**
 * Yab Framework
 *  
 * @category   Yab_File
 * @package    Yab_File_Xml
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_File_Xml extends Yab_File_Csv {

	private $_item_tag = 'item';
	private $_items_tag = 'items';
	
	final public function setItemTag($item_tag) {

		$this->_item_tag = $item_tag;

		return $this;

	}

	final public function setItemsTag($items_tag) {

		$this->_items_tag = $items_tag;

		return $this;

	}
	
	protected function getContent() {

		if($this->_content !== null)
			return $this->_content;
	
		$filter_html = new Yab_Filter_Html();
		
		$this->_content = '<'.'?xml version="1.0" encoding="'.$this->getEncoding().'" ?'.'>'.$this->getLineEnding();
		$this->_content .= '<'.$this->_items_tag.'>'.$this->getLineEnding();

		$datas = $this->getDatas();
		$fields = $this->getFields();

		foreach($datas as $data) {

			$this->_content .= "\t".'<'.$this->_item_tag.'>'.$this->getLineEnding();
		
			foreach($fields as $key => $value) {

				if(is_numeric($key))
					$key = $value;

				$data[$key] = $this->_callback($data, $key);

				$this->_content .= "\t\t".'<'.$key.'>'.htmlspecialchars(is_array($data[$key]) ? implode(', ', $data[$key]) : $data[$key], ENT_QUOTES, $this->getEncoding()).'</'.$key.'>'.$this->getLineEnding();

			}
		
			$this->_content .= "\t".'</'.$this->_item_tag.'>'.$this->getLineEnding();

		}
		
		$this->_content .= '</'.$this->_items_tag.'>'.$this->getLineEnding();

		$this->_content = $this->encode($this->_content);
		$this->_content = $this->convert($this->_content);

		return $this->_content;

	}

}

// Do not clause PHP tags unless it is really necessary