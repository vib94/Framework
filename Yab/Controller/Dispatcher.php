<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller
 * @package    Yab_Controller_Dispatcher
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework
 */

class Yab_Controller_Dispatcher {

	private $_plugins = array();

	final public function addPlugin($plugin_class) {

		$loader = Yab_Loader::getInstance();
	
		try {

			$this->_plugins[$plugin_class] = $loader->invoke($plugin_class, array(), 'Yab_Controller_Plugin_Abstract');
			
		} catch(Yab_Exception $e) {

			$this->_plugins[$plugin_class] = $loader->invoke('Yab_Controller_Plugin_'.$plugin_class, array(), 'Yab_Controller_Plugin_Abstract');
		
		}

		return $this;

	}

	final public function removePlugin($plugin_class) {

		if(array_key_exists($plugin_class, $this->_plugins))
			unset($this->_plugins[$plugin_class]);

		return $this;

	}

	final public function dispatch(Yab_Controller_Request $request, Yab_Controller_Response $response) {

		$loader = Yab_Loader::getInstance();

		foreach($this->_plugins as $plugin)
			$plugin->preDispatch($request, $response);

		$controller = $loader->invoke($request->getControllerClass(), array($request, $response), 'Yab_Controller_Action');

		$controller->preDispatch();

		$loader->invoke($controller, $request->getActionMethod(), $request->getParams());

		foreach($this->_plugins as $plugin)
			$plugin->preRender($request, $response);

		$controller->preRender();
	
		$controller->render();

		$controller->postRender();

		foreach($this->_plugins as $plugin)
			$plugin->postRender($request, $response);

		$controller->renderLayout();

		$controller->postDispatch();

		foreach($this->_plugins as $plugin)
			$plugin->postDispatch($request, $response);

		return $this;

	}

}

// Do not clause PHP tags unless it is really necessary