<?php
/**
 * Yab Framework
 *
 * @category   Yab_Cache_Adapter
 * @package    Yab_Cache_Adapter_File
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Cache_Adapter_File extends Yab_Cache_Adapter_Abstract {

	private $_directory = null;

	public function __construct($directory) {

		$this->_directory = realpath($directory);

		if(!$this->_directory || !is_dir($this->_directory) || !is_writable($this->_directory))
			throw new Yab_Exception('['.$directory.'] is not a valid writable cache directory');

	}

	public function set($key, $value, $ttl = self::DEFAULT_TTL) {

		$key = trim($key, DIRECTORY_SEPARATOR);

		$folders = explode(DIRECTORY_SEPARATOR, $key);
		
		$file = array_pop($folders);
		
		$directory = $this->_directory;
		
		foreach($folders as $folder) {
		
			$directory .= DIRECTORY_SEPARATOR.$folder;
		
			if(!is_dir($directory) || !is_writable($directory))
				mkdir($directory, 0755);
		
			if(!is_dir($directory) || !is_writable($directory))
				throw new Yab_Exception('can not create directory ['.$directory.']');
		
		}
		
		$file = $directory.DIRECTORY_SEPARATOR.$file;

		@file_put_contents($file, serialize($value));
	
		if(!is_file($file) || !is_readable($file) || !is_writable($file))
			throw new Yab_Exception('can not create file ['.$file.']');

		@touch($file, time() + $ttl);

		return $this;

	}

	public function get($key) {

		$key = trim($key, DIRECTORY_SEPARATOR);

		if(!is_file($this->_directory.DIRECTORY_SEPARATOR.$key) || filemtime($this->_directory.DIRECTORY_SEPARATOR.$key) < time())
			return null;

		return unserialize(file_get_contents($this->_directory.DIRECTORY_SEPARATOR.$key));

	}

	public function flush() {

		$dh = opendir($this->_directory);

		if($dh) {

			while($f = readdir($dh)) {

				if(is_file($this->_directory.DIRECTORY_SEPARATOR.$f))
					unlink($this->_directory.DIRECTORY_SEPARATOR.$f);

			}

			closedir($dh);

		}

		return $this;

	}

	public function close() {

		return $this;

	}

}

// Do not clause PHP tags unless it is really necessary