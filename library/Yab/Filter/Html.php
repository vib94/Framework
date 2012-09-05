<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Html
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Html extends Yab_Filter_Abstract {

	public function _filter($value) {

		mb_detect_order('ASCII, UTF-8, ISO-8859-15, ISO-8859-1, WINDOWS-1252, JIS, EUC-JP, SJIS');

		$encoding = mb_detect_encoding($value);

		if($encoding != 'UTF-8') {

			$value = strtr($value, array(
				chr(145) => "'",
				chr(146) => "'",
				chr(147) => '"',
				chr(148) => '"',
				chr(151) => '-',
			));
		
			$value = mb_convert_encoding($value, 'UTF-8', $encoding ? $encoding : 'auto');
		
		}
		
		if(!$value)
			return $value;

		$html = htmlentities($value, ENT_QUOTES, 'UTF-8');

		if($html)
			return $html;

		return htmlentities($value, ENT_QUOTES);

	}

}

// Do not clause PHP tags unless it is really necessary