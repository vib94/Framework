<?php
/**
 * Yab Framework
 *  
 * @category   Yab_File
 * @package    Yab_File_Json
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_File_Json extends Yab_File_Csv {

	protected function getContent() {

		if($this->_content !== null)
			return $this->_content;
	
		$filter_html = new Yab_Filter_Html();

		$datas = $this->getDatas();

		if($datas instanceof Yab_Db_Statement)
			$datas = $datas->setValue(array())->toArray();
		
		$this->_content = json_encode($datas);

		$this->_content = $this->encode($this->_content);
		$this->_content = $this->convert($this->_content);

		return $this->_content;

	}

}

// Do not clause PHP tags unless it is really necessary