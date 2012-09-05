<?php
/**
 * Yab Framework
 *
 * @category   Yab_I18n_Adapter
 * @package    Yab_I18n_Adapter_Ini
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

 class Yab_I18n_Adapter_Ini extends Yab_I18n_Adapter_Abstract {

	private $_file = null;
	
	private $_datas = array();
	
	final public function setFile($file) {

		$file = trim((string) $file);
		
		if(!Yab_Loader::getInstance()->isFile($file))
			throw new Yab_Exception('"'.$file.'" is not a valid readable language file');
	
		$this->_file = $file;

		$this->_datas = parse_ini_file($this->_file, true);

		return $this;
	
	}
 
	final public function getFile() {

		return $this->_file;
	
	}
	
	protected function _say($key) {
	
		$key = (string) $key;
	
		if(!array_key_exists($key, $this->_datas))
			throw new Yab_Exception('"'.$key.'" is not defined in the language file');
			
		$languages = $this->_datas[$key];
	
		if(!array_key_exists($this->_language, $languages))
			throw new Yab_Exception('"'.$this->_language.'" is not a defined language for this key in the language file');

		return $languages[$this->_language];
	
	}

}
 
// Do not clause PHP tags unless it is really necessary