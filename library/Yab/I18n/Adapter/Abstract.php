<?php
/**
 * Yab Framework
 *
 * @category   Yab_I18n
 * @package    Yab_I18n_Adapter
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

abstract class Yab_I18n_Adapter_Abstract {

	protected $_language = null;

	final public function setLanguage($language) {

		$this->_language = trim((string) $language);

		return $this;

	}

	final public function getLanguage() {

		return $this->_language;

	}

	final public function say($key, array $parameters = array()) {

		$value = $this->_say($key);

		$i = 1;
		
		while(count($parameters)) {
		
			$param = array_shift($parameters);
		
			$value = preg_replace('#(\$'.$i.')([^0-9]|$)#', $param.'$2', $value);

			$i++;
		
		}

		return $value;

	}

	abstract protected function _say($key);

}

// Do not clause PHP tags unless it is really necessary