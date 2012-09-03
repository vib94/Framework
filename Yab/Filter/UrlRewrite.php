<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_UrlRewrite
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_UrlRewrite extends Yab_Filter_Abstract {

  public function _filter($value) {
  
    $delimiter = $this->has('delimiter') ? $this->get('delimiter') : '-';
  
    $value = preg_replace("/&(.)(grave|acute|cedil|circ|ring|tilde|uml|[0-9]{3});/", '\\1', strtolower(htmlentities($value, ENT_QUOTES, 'UTF-8')));
    $value = preg_replace("/([^a-z0-9]+)/", $delimiter, html_entity_decode($value));

    while(is_numeric(strpos($value, $delimiter.$delimiter)))
      $value = str_replace($delimiter.$delimiter, $delimiter, $value);

    $value = trim($value, $delimiter);

    return $value;
  
  }

}

// Do not clause PHP tags unless it is really necessary