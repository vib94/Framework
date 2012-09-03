<?php
/**
 * Yab Framework
 *
 * @category   Yab
 * @package    Yab_Version
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Version {

	private $_repository = null;

	public function __construct() {

		$this->_repository = dirname(__FILE__);

	}

	public function getVersion($directory = null, $initial_directory = null) {

		if($directory === null)
			$directory = $this->_repository;

		if($initial_directory === null)
			$initial_directory = $directory;

		$version = array();

		$it = new DirectoryIterator($directory);

		foreach($it as $file) {

			if($file->isDot())
				continue;

			if(preg_match('#\.svn#i', $file->getFilename()))
				continue;

			if($file->isDir())
				$version = array_merge($version, $this->getVersion($directory.DIRECTORY_SEPARATOR.$file->getFilename(), $initial_directory));

			if($file->isFile())
				$version[str_replace($initial_directory, '', $directory.DIRECTORY_SEPARATOR.$file->getFilename())] = md5(file_get_contents($directory.DIRECTORY_SEPARATOR.$file->getFilename()));

		}

		return $version;

	}

	public function compare($directory) {

		$self_version = $this->getVersion();
		$directory_version = $this->getVersion($directory);

		foreach($self_version as $key => $value) {

			$directory_value = null;

			if(array_key_exists($key, $directory_version)) {

				$directory_value = $directory_version[$key];

				unset($directory_version[$key]);

			}

			if($directory_value === null) {

				$self_version[$key] = 'DELETED';

			} elseif($directory_value === $value) {

				$self_version[$key] = 'EQUAL';

			} else {

				$self_version[$key] = 'DIFFERENT';

			}

		}

		foreach($directory_version as $key => $value)
			$self_version[$key] = 'ADDED';

		return '<pre>'.print_r($self_version, true).'</pre>';

	}

	public function __toString() {

		return '<pre>'.print_r($this->getVersion(), true).'</pre>';

	}

}

// Do not clause PHP tags unless it is really necessary