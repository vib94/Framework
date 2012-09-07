<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller
 * @package    Yab_Controller_Router
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework
 */

class Yab_Controller_Router {

	private $_file = null;

	private $_default_controller = 'Index';
	private $_error_controller = 'Error';
	private $_default_action = 'index';

	final public function setDefaultController($default_controller) {

		$this->_default_controller = $default_controller;

		return $this;

	}

	final public function setErrorController($error_controller) {

		$this->_error_controller = $error_controller;

		return $this;

	}

	final public function setDefaultAction($default_action) {

		$this->_default_action = $default_action;

		return $this;

	}

	final public function getDefaultController() {

		return $this->_default_controller;

	}

	final public function getErrorController() {

		return $this->_error_controller;

	}

	final public function getDefaultAction() {

		return $this->_default_action;

	}

	final public function setFile($file) {

		$file = trim((string) $file);

		if(!Yab_Loader::getInstance()->isFile($file))
			throw new Yab_Exception($file.' is not an existing readable routes file');

		$this->_file = $file;

		return $this;

	}

	final public function getFile() {

		return $this->_file;

	}

	final public function route(Yab_Controller_Request $request) {

		if(!$request->isRouted() && $this->_file) {

			$fh = fopen($this->_file, 'rt', true);

			while(!feof($fh) && !$request->isRouted()) {

				$route = trim(fgets($fh, 1024));

				if(!$route)
					continue;

				if(strpos($route, '#') === 0)
					continue;

				if($this->_route($request, $route))
					break;

			}

			fclose($fh);

		}

		if(!$request->isRouted()) 
			$this->_route($request, $request->getUri().'=');
	
		return $this;

	}
	
	final private function _route(Yab_Controller_Request $request, $route) {

	
		$equal = strrpos($route, '=');
		
		if(!is_numeric($equal))
			throw new Yab_Exception('"'.$route.'" is not a valid route ( no equal )');

		$external = trim(substr($route, 0, $equal));	
		$internal = trim(substr($route, $equal + 1));	
		
		$external_regexp = '#^'.str_replace('\*', '([a-zA-Z0-9_-]*)',  preg_quote($external, '#')).'#';

		if($internal) {
		
			$first_comma = strpos($internal, '.');
			
			if(!is_numeric($first_comma))
				throw new Yab_Exception('"'.$route.'" is not a valid route ( no dot )');

			$opened_parenthesis = strpos($internal, '(');
			
			if(!is_numeric($opened_parenthesis))
				throw new Yab_Exception('"'.$route.'" is not a valid route ( no opened_parenthesis )');

			$closed_parenthesis = strpos($internal, ')');
			
			if(!is_numeric($closed_parenthesis))
				throw new Yab_Exception('"'.$route.'" is not a valid route ( no closed_parenthesis )');	

			$internal_controller = trim(substr($internal, 0, $first_comma));		
			$internal_action = trim(substr($internal, $first_comma + 1, $opened_parenthesis - $first_comma - 1));		
			$internal_params = trim(substr($internal, $opened_parenthesis + 1, $closed_parenthesis - $opened_parenthesis - 1));

			if(preg_match_all('#\$[0-9]+#', $internal_params, $match) < substr_count($external, '*'))  
				throw new Yab_Exception('Wrong paramaeters count in this route');

			$internal_params = $internal_params ? array_map('trim', explode(',', $internal_params)) : array();

			if($request->getUri()) {

				if(!preg_match($external_regexp, $request->getUri(), $matches))
					return false;

				foreach($internal_params as $key => $value) 
					$internal_params[$key] = urldecode(preg_match('#^\$([0-9]+)$#', $value, $match) ? $matches[$match[1]] : trim($value, '"\''));

				$request->setController($internal_controller);
				$request->setAction($internal_action);
				$request->setParams($internal_params);
			
				$this->_checkRequest($request);
	
				return true;

			}
			
			if($request->getController() != $internal_controller || $request->getAction() != $internal_action || count($request->getParams()) != count($internal_params))
				return false;

			$i = 1;
			
			$pos = strpos($external, '*');

			while(is_numeric($pos)) {
				
				$length = 0;
				
				foreach($internal_params as $key => $internalParam) {
			
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

		if($request->getUri()) {

			if(!preg_match($external_regexp, $request->getUri(), $matches))
				return false;
	
			$parts = trim($request->getUri(false), '/');
			$parts = $parts ? explode('/', $parts) : array();
			
			$parts = array_map('urldecode', $parts);
	
			$request->setController(count($parts) ? array_shift($parts) : $this->_default_controller);
			$request->setAction(count($parts) ? array_shift($parts) : $this->_default_action);
			$request->setParams($parts);
			
			$this->_checkRequest($request);

			return true;
	
		}

		$request->setUri($request->getBaseUrl().'/'.$request->getController().'/'.$request->getAction().'/'.implode('/', $request->getParams()));
		
		return true;

	}
	
	private function _checkRequest(Yab_Controller_Request $request) {
	
		try {
			
			$class = $request->getControllerClass();
			$method = $request->getActionMethod();

			if(!class_exists($class) || !method_exists($class, $method))
				throw new Yab_Exception('no route');

		} catch(Yab_Exception $e) {
		
			$request->setController($this->_error_controller)->setAction($this->_default_action)->setParams(array());
		
		}
		
		return $this;
	
	}

}

// Do not clause PHP tags unless it is really necessary