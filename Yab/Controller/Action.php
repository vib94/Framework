<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller
 * @package    Yab_Controller_Action
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Controller_Action {
	
	# use Yab_Mvc;

	protected $_request = null;
	protected $_response = null;

	protected $_view = null;

	final public function __construct(Yab_Controller_Request $request, Yab_Controller_Response $response) {

		$this->_request = $request;
		$this->_response = $response;
		
		$this->_view = new Yab_View();
		
		$this->_layout = Yab_Loader::getInstance()->getLayout()->setEnabled(!$request->isAjax());
		
		try {
		
			$filter_lc = new Yab_Filter_LowerCase(array('separator' => '_'));

			$controller = $filter_lc->filter(str_replace('_', DIRECTORY_SEPARATOR, $request->getController()));
			
			$action = $filter_lc->filter($request->getAction());
		
			$this->_view->setFile('View'.DIRECTORY_SEPARATOR.$controller.DIRECTORY_SEPARATOR.$action.'.html');
		
		} catch(Yab_Exception $e) {
		
			# No default view
		
		}

		$this->_view->set('layout', $this->_layout);
		$this->_view->set('request', $this->_request);
		$this->_view->set('response', $this->_response);
		$this->_view->set('base_url', $this->_request->getBaseUrl());

		$this->_init();

	}

	protected function _init() {}

	public function preDispatch() {}
	
	public function preRender() {}
	
	public function render() {
	
		$this->_view->bufferize();
	
		$this->_response->append((string) $this->_view);
		
		return $this;
	
	}
	
	public function postRender() {}
	
	public function renderLayout() {
	
		if(!$this->_layout->getEnabled() || !$this->_layout->getFile())
			return $this;

		$this->_layout->populate($this->_view->toArray());
		$this->_layout->set('view', (string) $this->_response);
		$this->_layout->bufferize();
	
		$this->_response->clear()->append((string) $this->_layout);
		
		return $this;
	
	}
	
	public function postDispatch() {}

	final public function getView() {

		return $this->_view;

	}
	
	# Proxy to loader
	final public function __call($method, array $args) {
	
		$loader = Yab_Loader::getInstance();

		return $loader->invoke($loader, $method, $args);

	}

}

// Do not clause PHP tags unless it is really necessary