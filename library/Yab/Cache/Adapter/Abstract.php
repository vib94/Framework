<?php
/**
 * Yab Framework
 *
 * @category   Yab_Cache_Adapter
 * @package    Yab_Cache_Adapter_Abstract
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

abstract class Yab_Cache_Adapter_Abstract {

	const DEFAULT_TTL = 60;

	private $_prefix = null;

	abstract public function set($key, $value, $ttl = self::DEFAULT_TTL);
	abstract public function get($key);
	abstract public function flush();
	abstract public function close();

	public function getPrefix($prefix) {

		return $this->_prefix;

	}

	public function setPrefix($prefix) {

		$this->_prefix = $prefix;

		return $this;

	}

	final public function __destruct() {

		$this->close();

	}

}

// Do not clause PHP tags unless it is really necessary