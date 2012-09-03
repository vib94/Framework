<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_QueryString
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_QueryString extends Yab_Filter_Abstract {

	public function _filter($value) {
  
		$queryString = array();
		
		foreach($value as $key => $value) {
		
			if(is_array($value)) {
		
				$val = array();
		
				foreach($value as $v) 
					array_push($val, urlencode($key).'[]='.urlencode($v));
		
				array_push($queryString, implode('&', $val));

			} else {
		
				array_push($queryString, urlencode($key).'='.urlencode($value));
		
			}
		
		}
		
		return implode('&', $queryString);
  
	}

}

// Do not clause PHP tags unless it is really necessary