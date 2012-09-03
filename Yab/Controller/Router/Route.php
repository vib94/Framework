<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller_Router
 * @package    Yab_Controller_Router_Route
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Controller_Router_Route {

	private $_route = '';
	private $_prefix = '';
	private $_external = '';
	private $_internal = '';
	private $_internal_controller = '';
	private $_internal_action = '';
	private $_internal_params = array();

	public function __construct($route) {

		$this->_route = trim((string) $route);		

		$equal = strrpos($this->_route, '=');
		
		if(!is_numeric($equal))
			throw new Yab_Exception($route.' is not a valid route ( no equal )');

		$this->_external = trim(substr($route, 0, $equal));		
		$this->_internal = trim(substr($route, $equal + 1));	
		
		$first_comma = strpos($this->_internal, '.');
		
		if(!is_numeric($first_comma))
			throw new Yab_Exception($first_comma.' is not a valid route ( no dot )');

		$opened_parenthesis = strpos($this->_internal, '(');
		
		if(!is_numeric($opened_parenthesis))
			throw new Yab_Exception($opened_parenthesis.' is not a valid route ( no opened_parenthesis )');

		$closed_parenthesis = strpos($this->_internal, ')');
		
		if(!is_numeric($closed_parenthesis))
			throw new Yab_Exception($closed_parenthesis.' is not a valid route ( no closed_parenthesis )');	

		$this->_internal_controller = trim(substr($this->_internal, 0, $first_comma));		
		$this->_internal_action = trim(substr($this->_internal, $first_comma + 1, $opened_parenthesis - $first_comma - 1));		
		$this->_internal_params = trim(substr($this->_internal, $opened_parenthesis + 1, $closed_parenthesis - $opened_parenthesis - 1));

		if(preg_match_all('#\$[0-9]+#', $this->_internal_params, $match) < substr_count($this->_external, '*'))  
			throw new Yab_Exception('Wrong paramaeters count in this route');

		$this->_internal_params = $this->_internal_params ? array_map('trim', explode(',', $this->_internal_params)) : array();

	}
	
	public function route(Yab_Controller_Request $request) {

		if($request->getUri()) {

			$external = preg_quote($this->_external, '#');
			$external = str_replace('\*', '([a-zA-Z0-9_-]*)',  $external);

			if(!preg_match('#^'.$external.'#', $request->getUri(), $matches))
				return false;

			foreach($this->_internal_params as $key => $value) 
				$this->_internal_params[$key] = urldecode(preg_match('#^\$([0-9]+)$#', $value, $match) ? $matches[$match[1]] : trim($value, '"\''));

			$request->setController($this->_internal_controller);
			$request->setAction($this->_internal_action);
			$request->setParams($this->_internal_params);

			return true;

		}

		if($request->getController() != $this->_internal_controller)
			return false;

		if($request->getAction() != $this->_internal_action)
			return false;

		if(count($request->getParams()) != count($this->_internal_params))
			return false;

		$external = $this->_external;

		$i = 1;
		
		$pos = strpos($external, '*');

		while(is_numeric($pos)) {
			
			$length = 0;
			
			foreach($this->_internal_params as $key => $internalParam) {
		
				if(!preg_match('#^\$([0-9]+)$#', $internalParam, $match))
					continue;
				
				if($i != $match[1])
					continue;
											
				$external = substr($external, 0, $pos).$request->getParam($key).substr($external, $pos + 1); 
				
				$length = strlen($request->getParam($key));
				
				break;

			}
			
			if(!$length)
				break;

			$pos = strpos($external, '*', $pos + $length);

			$i++;

		}

		$request->setUri($request->getBaseUrl().$external);

		return true;

	}

}

// Do not clause PHP tags unless it is really necessary