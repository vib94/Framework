<?php
/**
 * Yab Framework
 *
 * @category   Yab_Cache_Adapter
 * @package    Yab_Cache_Adapter_Memcache
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Cache_Adapter_Memcache extends Yab_Cache_Adapter_Abstract {

	private $_memcache = null;

	public function __construct($host, $port) {

		if(!class_exists('Memcache'))
			throw new Yab_Exception('Class Memcache does not exists');

		$this->_memcache = new Memcache();

		if(!@$this->_memcache->connect($host, $port))
			throw new Yab_Exception('Can not connect to Memcache server');

	}

	public function set($key, $value, $ttl = self::DEFAULT_TTL) {

		// On utilise @ car il existe un bug (notice) dans les version <= 3.0.1 -> error=11
		return @$this->_memcache->set($key, $value, null, $ttl);

	}

	public function get($key) {

		return @$this->_memcache->get($key);

	}

	public function flush() {

		return @$this->_memcache->flush();

	}

	public function close() {

		return @$this->_memcache->close();

	}

}

// Do not clause PHP tags unless it is really necessary