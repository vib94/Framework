<?php
/**
 * Yab Framework
 *
 * @category   Yab
 * @package    Yab_View
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_View extends Yab_Object {
	
	# use Yab_Mvc;

	private $_file = null;
	private $_buffer = null;
	private $_enabled = true;

	final public function setFile($file) {

		if(!$file) {
		
			$this->_file = null;
			
			return $this;
		
		}
	
		$file = trim((string) $file);

		if(!Yab_Loader::getInstance()->isFile($file))
			throw new Yab_Exception($file.' is not an existing readable view file');
		
		$this->_file = $file;

		return $this;

	}

	final public function setEnabled($enabled) {

		$this->_enabled = (bool) $enabled;

		return $this;

	}

	final public function getFile() {

		return $this->_file;

	}

	final public function getEnabled() {

		return $this->_enabled;

	}

	final public function clear() {

		$this->_buffer = null;

		return $this;

	}

	public function bufferize($buffer = null) {
	
		if($this->_enabled && $buffer !== null)
			$this->_buffer = (string) $buffer;

		if(!$this->_enabled || $this->_buffer !== null || $this->_file === null)
			return $this;
		
		ob_start();

		extract($this->getAttributes());

		include $this->_file;

		$this->_buffer = ob_get_clean();
		
		return $this;

	}	

	final public function __toString() {

		return (string) $this->_buffer;

	}	

	# Proxy to loader
	final public function __call($method, array $args) {
	
		$loader = Yab_Loader::getInstance();

		return $loader->invoke($loader, $method, $args);

	}

}

// Do not clause PHP tags unless it is really necessary