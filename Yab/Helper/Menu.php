<?php
/**
 * Yab Framework
 *  
 * @category   Yab_Helper
 * @package    Yab_Helper_Menu
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Helper_Menu {

	private $_match = null;
	private $_request = null;
	private $_label = null;
	private $_visible = true;
	private $_childs = array();

	public function __construct(Yab_Controller_Request $request = null, $label = null, $match = null, $visible = true, array $childs = array()) {

		if($request)
			$this->setRequest($request);

		if($label)
			$this->setLabel($label);

		if($match)
			$this->setMatch($match);

		$this->setVisible($visible);

		if(count($childs))
			$this->setChilds($childs);

	}

	public function setMatch($match) {

		$this->_match = (string) $match;

		return $this;

	}

	public function getMatch() {

		return (string) $this->_match;

	}

	public function setRequest(Yab_Controller_Request $request) {

		$this->_request = $request;

		return $this;

	}

	public function getRequest() {

		return $this->_request;

	}

	public function setLabel($label) {

		$this->_label = (string) $label;

		return $this;

	}

	public function getLabel() {

	return (string) $this->_label;

	}

	public function setVisible($visible) {

		$this->_visible = (bool) $visible;

		return $this;

	}

	public function getVisible() {

		return (bool) $this->_visible;

	}

	public function setChilds(array $childs) {

		$this->_childs = $childs;

		return $this;

	}

	public function getChilds() {

		return $this->_childs;

	}

	protected function match(Yab_Controller_Request $request) {

		if(!$this->_visible)
			return false;

		if(((string) $request) == ((string) $this->_request))
			return true;

		if($this->_match && preg_match($this->_match, (string) $request))
			return true;

		return false;

	}

	public function getChild(Yab_Controller_Request $request, $depth = 0, $current_depth = 0) {

		if($depth && $depth <= $current_depth)
			throw new Yab_Exception($request.' is not an existing child "'.$depth.' <= '.$current_depth.'"');

		if($this->_visible && ((string) $request) == ((string) $this->_request))
			return $this;

		foreach($this->_childs as $child) {

			try {

				return $child->getChild($request, $depth, $current_depth + 1);

			} catch(Yab_Exception $e) {

				continue;

			}

		}

		if($this->_visible && $this->_match && preg_match($this->_match, (string) $request))
			return $this;

		throw new Yab_Exception($request.' is not an existing child "no more childs"');

	}

	public function getHtml(Yab_Controller_Request $request, $depth = 0, $current_depth = 0, $first = true) {

		$html = '';

		if(!$this->_visible)
			return $html;

		if($this->_label)
			$html .= '<a href="'.$this->_request.'"'.(!$current_depth ? ' class="navigation_title"' : '').'>'.$this->_label.'</a>';

		if($depth && $depth <= $current_depth)
			return $html;

		if(count($this->_childs)) {

			$html .= PHP_EOL.'<ul>'.PHP_EOL;

			foreach($this->_childs as $child) {

				$child_html = $child->getHtml($request, $depth, $current_depth + 1, $first);

				if(!$child_html)
					continue;

				$html .= "\t".'<li class="'.$child->getRequest()->getController().' '.$child->getRequest()->getAction().($child->match($request) ? ' selected' : '').($first ? ' first' : '').'">'.$child_html.'</li>'.PHP_EOL;

				$first = false;

			}

			$html .= '</ul>'.PHP_EOL;

		}

		return $html;

	}

}

// Do not clause PHP tags unless it is really necessary