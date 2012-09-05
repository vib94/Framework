<?php
/**
 * Yab Framework
 *
 * @category   Yab_Controller
 * @package    Yab_Controller_Response
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Controller_Response {

	private $_headers = array();

	private $_body = null;
	
	private $_httpStatusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		118 => 'Connection timed out',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		210 => 'Content Different',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Moved Temporarily',
		303 => 'See Other',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		310 => 'Too many Redirects',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable entity',
		423 => 'Locked',
		424 => 'Method failure',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient storage',
		509 => 'Bandwidth Limit Exceeded',
	);

	final public function clear() {

		$this->_body = null;

		return $this;

	}

	final public function append($body) {

		$body = (string) $body;

		$this->_body = $this->_body.$body;

		return $this;

	}

	final public function prepend($body) {

		$body = (string) $body;

		$this->_body = $body.$this->_body;

		return $this;

	}

	final public function addHeader($name, $header = null) {

		if($header === null) {
		
			$header = explode(':', $name);
			
			$name = array_shift($header);
			
			$header = implode(':', $header);
		
		}

		$this->_headers[$name] = (string) $header;

		return $this;

	}

	final public function setStatusCode($code) {

		if(!array_key_exists($code, $this->_httpStatusCodes))
			throw new Yab_Exception('['.$code.'] is not a valid HTTP Status Code');

		foreach($this->_headers as $name => $header) {
			
			if(preg_match('#^HTTP/1\.0\s+[0-9]+#i', $name))
				unset($this->_headers[$name]);
		
		}
	
		return $this->addHeader('HTTP/1.0 '.$code.' '.$this->_httpStatusCodes[$code]);

	}

	final public function redirect($uri, $code = null) {

		if($code !== null)
			$this->setStatusCode($code);

		return $this->addHeader('Location', $uri)->clear()->send();

	}

	final public function send() {

		foreach($this->_headers as $name => $header)
			header($header ? $name.': '.$header : $name);

		echo (string) $this->_body;

		exit();

	}

	final public function __toString() {

		return (string) $this->_body;

	}

	final public function toHttp() {

		$http = '';
	
		foreach($this->_headers as $name => $header)
			$http .= $name.': '.$header.PHP_EOL;
		
		$http .= PHP_EOL;

		$http .= (string) $this->_body;

		return $http;

	}

	final public function enableCache($since, $time = '+1 day') {

		if(!is_integer($time))
			$time = strtotime($time);

		$this->_headers += array(
			'Date' => gmdate("D, j M Y G:i:s ", time()) . 'GMT',
			'Last-Modified' => gmdate("D, j M Y G:i:s ", $since) . 'GMT',
			'Expires' => gmdate("D, j M Y H:i:s", $time) . " GMT",
			'Cache-Control' => 'public, max-age=' . ($time - time()),
			'Pragma' => 'cache'
		);

		return $this;

	}

	final public function disableCache() {

		$this->_headers += array(
			'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
			'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
			'Pragma' => 'no-cache'
		);

		return $this;
		
	}

	final public function download(Yab_File $file) {

		$this->_headers += array(
			'Content-type' => $file->getMimeType().'; charset='.strtolower($file->getEncoding()),
			'Content-Disposition' => 'attachment; filename='.basename($file->getPath()),
			'Expires' => '0',
			'Pragma' => 'no-cache'
		);

		return $this->clear()->append($file)->send();
		
	}

}

// Do not clause PHP tags unless it is really necessary