<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Encrypt
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Encrypt extends Yab_Filter_Abstract {

	public function _filter($value) {

		$strlen_value = strlen($value);

		$new_value = '';
		
		if(0 < $strlen_value) {
	
			$offset = intval(($strlen_value / 2) + ($strlen_value / 3) + ($strlen_value / 4));

			for($i = 0; $i < $strlen_value; $i++) {
			
				$char = $value[$i];
			
				$ord = ord($char);
				
				$ord += $offset - $i - ($i % 2 ? 1 : 2);
			
				$char = chr($ord);
				
				$new_value .= $char;
		
			}
			
		}
		
		return strrev($new_value);

	}

}

// Do not clause PHP tags unless it is really necessary