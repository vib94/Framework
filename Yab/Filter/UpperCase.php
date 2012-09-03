<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_UpperCase
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_UpperCase extends Yab_Filter_Abstract {

	public function _filter($value) {

		if($this->has('separator')) {
		
			$separator = str_replace('\\', '\\\\', $this->get('separator'));
		
			$value = trim($value);
			$value = preg_replace('#([a-z0-9])([A-Z]+)#', '$1'.$separator.'$2', $value);
			$value = preg_replace('#'.preg_quote($separator, '#').'+#', $separator, $value);
			$value = trim($value, $separator);
		
		}
	
		return strtoupper($value);

	}

}

// Do not clause PHP tags unless it is really necessary