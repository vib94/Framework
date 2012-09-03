<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller_Plugin_Abstract
 * @package    Yab_Controller_Plugin_Decorator
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Controller_Plugin_Decorator extends Yab_Controller_Plugin_Abstract {

	public function preDispatch(Yab_Controller_Request $request, Yab_Controller_Response $response) {
	}
	
	public function postDispatch(Yab_Controller_Request $request, Yab_Controller_Response $response) {
	}

	public function preRender(Yab_Controller_Request $request, Yab_Controller_Response $response) {

		return $response->append('<!-- START '.$request->getControllerClass().'->'.$request->getActionMethod().'('.implode(', ', $request->getParams()).') -->'.PHP_EOL);

	}

	public function postRender(Yab_Controller_Request $request, Yab_Controller_Response $response) {

		return $response->append(PHP_EOL.'<!-- END '.$request->getControllerClass().'->'.$request->getActionMethod().'('.implode(', ', $request->getParams()).') -->');

	}

}

// Do not clause PHP tags unless it is really necessary