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

		if(!$request->isRouted()) {

			if($request->getUri()) {

				$parts = trim($request->getUri(false), '/');
				$parts = $parts ? explode('/', $parts) : array();
				
				$parts = array_map('urldecode', $parts);

				try {
			
					$request->setController(count($parts) ? array_shift($parts) : $this->_default_controller);
					$request->setAction(count($parts) ? array_shift($parts) : $this->_default_action);
					$request->setParams($parts);
					
					$class = $request->getControllerClass();
					$method = $request->getActionMethod();

					if(!class_exists($class) || !method_exists($class, $method))
						throw new Yab_Exception('no route');
						
				} catch(Yab_Exception $e) {
				
					# continue to file router
				
				}

			} else {

				$request->setUri($request->getBaseUrl().'/'.$request->getController().'/'.$request->getAction().'/'.implode('/', $request->getParams()));

			}

		}

		if(!$request->isRouted() && $this->_file) {

			$fh = fopen($this->_file, 'rt', true);

			while(!feof($fh) && !$request->isRouted()) {

				$route = trim(fgets($fh, 1024));

				if(!$route)
					continue;

				if(strpos($route, '#') === 0)
					continue;

				$route = new Yab_Controller_Router_Route($route);

				$route->route($request);

			}

			fclose($fh);

		}

		if(!$request->isRouted())
			$request->setController($this->_error_controller)->setAction($this->_default_action)->setParams(array());
				
		return $this;

	}

}

// Do not clause PHP tags unless it is really necessary