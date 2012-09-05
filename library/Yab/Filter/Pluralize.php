<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Pluralize
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Pluralize extends Yab_Filter_Abstract {
	
	private $_plural = array(
		'/(quiz)$/i' => '$1zes',
		'/^(ox)$/i' => '$1en',
		'/([m|l])ouse$/i' => '$1ice',
		'/(matr|vert|ind)ix|ex$/i' => '$1ices',
		'/(x|ch|ss|sh)$/i' => '$1es',
		'/([^aeiouy]|qu)y$/i' => '$1ies',
		'/(hive)$/i' => '$1s',
		'/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
		'/(shea|lea|loa|thie)f$/i' => '$1ves',
		'/sis$/i' => 'ses',
		'/([ti])um$/i' => '$1a',
		'/(tomat|potat|ech|her|vet)o$/i' => '$1oes',
		'/(bu)s$/i' => '$1ses',
		'/(alias)$/i' => '$1es',
		'/(octop)us$/i' => '$1i',
		'/(ax|test)is$/i' => '$1es',
		'/(us)$/i' => '$1es',
		'/s$/i' => 's',
		'/$/' => 's',
	);
	
	private $_irregular = array(
		'move' => 'moves',
		'foot' => 'feet',
		'goose' => 'geese',
		'sex' => 'sexes',
		'child' => 'children',
		'man' => 'men',
		'tooth' => 'teeth',
		'person' => 'people',
	);
	
	private $_uncountable = array(
		'sheep',
		'fish',
		'deer',
		'series',
		'species',
		'money',
		'rice',
		'information',
		'equipment',
	);
	
	public function _filter($value) {
		
		if(in_array(strtolower($value), $this->_uncountable))
			return $value;
		
		foreach($this->_irregular as $pattern => $result) {
			
			$pattern = '/'.$pattern.'$/i';
		
			if(preg_match($pattern, $value))
				return preg_replace($pattern, $result, $value);
				
		}
		
		foreach($this->_plural as $pattern => $result) {
			
			if(preg_match($pattern, $value))
				return preg_replace($pattern, $result, $value);
			
		}
		
		return $value;
		
	}
	
}

// Do not clause PHP tags unless it is really necessary