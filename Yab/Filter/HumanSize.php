<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_HumanSize
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_HumanSize extends Yab_Filter_Abstract {

  public function _filter($value) {
  
	  $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

    for($i = 0; $value > 1024; $i++) 
      $value /= 1024;
      
		return round($value, 2).' '.$units[$i];
  
  }

}

// Do not clause PHP tags unless it is really necessary