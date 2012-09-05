<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Singularize
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Singularize extends Yab_Filter_Abstract {

	private $_singular = array(
		'/(quiz)zes$/i' => '$1',
		'/(matr)ices$/i' => '$1ix',
		'/(vert|ind)ices$/i' => '$1ex',
		'/^(ox)en$/i' => '$1',
		'/(alias)es$/i' => '$1',
		'/(octop|vir)i$/i' => '$1us',
		'/(cris|ax|test)es$/i' => '$1is',
		'/(shoe)s$/i' => '$1',
		'/(o)es$/i' => '$1',
		'/(bus)es$/i' => '$1',
		'/([m|l])ice$/i' => '$1ouse',
		'/(x|ch|ss|sh)es$/i' => '$1',
		'/(m)ovies$/i' => '$1ovie',
		'/(s)eries$/i' => '$1eries',
		'/([^aeiouy]|qu)ies$/i' => '$1y',
		'/([lr])ves$/i' => '$1f',
		'/(tive)s$/i' => '$1',
		'/(hive)s$/i' => '$1',
		'/(li|wi|kni)ves$/i' => '$1fe',
		'/(shea|loa|lea|thie)ves$/i' => '$1f',
		'/(^analy)ses$/i' => '$1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '$1$2sis',
		'/([ti])a$/i' => '$1um',
		'/(n)ews$/i' => '$1ews',
		'/(h|bl)ouses$/i' => '$1ouse',
		'/(corpse)s$/i' => '$1',
		'/(us)es$/i' => '$1',
		'/s$/i' => '',
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
		
		foreach($this->_singular as $pattern => $result) {
			
			if(preg_match($pattern, $value))
				return preg_replace($pattern, $result, $value);
			
		}
		
		return $value;
		
	}
	
}

// Do not clause PHP tags unless it is really necessary