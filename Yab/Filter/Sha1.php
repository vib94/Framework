<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Sha1
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Sha1 extends Yab_Filter_Abstract {

  public function _filter($value) {
  
    return sha1($value);
  
  }

}

// Do not clause PHP tags unless it is really necessary