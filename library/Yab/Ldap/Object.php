<?php
/**
 * Yab Framework
 *
 * @category   Yab_Ldap
 * @package    Yab_Ldap_Object
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Ldap_Object extends Yab_Object {

	protected $_max_dig_group_depth = 5;

	private $_server = null;
	private $_group_cns = null;

	final public function __construct(Yab_Ldap_Server $server, $cn = null, $class = null) {

		$this->_server = $server;

		$search = '';

		if($cn !== null)
			$search .= '(cn='.$cn.')';

		if($class !== null)
			$search .= '(objectClass='.$class.')';

		if($search) {

			$infos = $this->_server->search('(&'.$search.')');

			if($infos['count'])
				$this->populate($infos[0]);

		}

	}

	final public function getServer() {

		return $this->_server;

	}

	final public function getGroups() {

		if($this->_group_cns !== null)
			return $this->_group_cns;

		try {
			
			$memberof = $this->get('memberof');
		
		} catch(Yab_Exception $e) {
		
			$memberof = array();
		
		}
		
		$this->_group_cns = $this->_digGroups($memberof);
		$this->_group_cns = array_unique($this->_group_cns);

		return $this->_group_cns;

	}
	
	final protected function _digGroups(array $memberof, $depth = 0) {

		$groups = array();
		
		if($depth <= $this->_max_dig_group_depth) {
		
			foreach($memberof as $line) {

				if(!preg_match('#^CN=([\-a-zA-Z0-9_\s]+),#i', $line, $matches))
					continue;
					
				$group_name = trim($matches[1]);

				array_push($groups, $group_name);
				
				$group = $this->_server->search('(&(cn='.$group_name.')(objectClass=Group))');
				
				if($group['count'])
					$group = $group[0];
				
				$memberof = array_key_exists('memberof', $group) ? $group['memberof'] : array();

				$sub_groups = $this->_digGroups($memberof, $depth + 1);
				
				$groups = array_merge($groups, $sub_groups);
				
			}
		
		}
	
		return array_unique($groups);
	
	}

	final public function hasGroup($cns) {

		if(!is_array($cns))
			$cns = array($cns);

		$groups = $this->getGroups();
		
		$groups = array_map('strtoupper', $groups);
			
		foreach($cns as $cn) {
			
			$cn = strtoupper($cn);
		
			if(in_array($cn, $groups))
				return true;

		}
		
		return false;

	}

	final public function getCount($key) {

		$value = $this->get($key);

		return $value['count'];

	}

	final public function getIndex($key, $index = 0) {

		$value = $this->get($key);

		return $value[$index];

	}

	final public function getFirst($key) {

		return $this->getIndex($key, 0);

	}

	final public function setMaxDigGroupDepth($max_dig_group_depth) {
	
		$this->_max_dig_group_depth = (int) $max_dig_group_depth;
		
		return $this;
	
	}
	
}

// Do not clause PHP tags unless it is really necessary