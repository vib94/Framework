<?php
/**
 * Yab Framework
 *
 * @category   Yab
 * @package    Yab_Session
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */
 
class Yab_Session extends Yab_Object {

	const SESSION_YAB_KEY = 'yab';

	public function __construct() {

		if(!isset($_SESSION))
			session_start();

		$_SESSION[self::SESSION_YAB_KEY] = 1;

		$this->_attributes = $_SESSION;		

	}
	
	public function sync() {
		
		$_SESSION = $this->_attributes;
		
		return $this;
		
	}

	public function __destruct() {

		return $this->sync();

	}

}

// Do not clause PHP tags unless it is really necessary