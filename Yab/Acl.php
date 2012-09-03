<?php
/**
 * Yab Framework
 *  
 * @category   Yab
 * @package    Yab_Acl
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */
 
class Yab_Acl {

	const DEFAULT_PRIVILEGE = 'allow';

	private $_roles = array();
	private $_ressources = array();

	final public function roleCan($role, $ressource, $privilege = self::DEFAULT_PRIVILEGE) {
	
		if(!array_key_exists($role, $this->_roles))
			return false;
	
		if(!array_key_exists($ressource, $this->_roles[$role]))
			return false;
	
		return in_array($privilege, $this->_roles[$role][$ressource]);
		
	}

	final public function ressourceHas($ressource, $role, $privilege = self::DEFAULT_PRIVILEGE) {
	
		if(!array_key_exists($ressource, $this->_ressources))
			return false;
	
		if(!array_key_exists($role, $this->_ressources[$ressource]))
			return false;

		return in_array($privilege, $this->_ressources[$ressource][$role]);
	
	}

	final public function roleExists($role) {
	
		return array_key_exists($role, $this->_roles);
	
	}

	final public function ressourceExists($ressource) {
	
		return array_key_exists($ressource, $this->_ressources);
	
	}

	final public function addRule($role, $ressource, $privilege = self::DEFAULT_PRIVILEGE) {

		$this->addRole($role, $ressource, $privilege);
		$this->addRessource($ressource, $role, $privilege);
	
		return $this;
	
	}
	
	final public function addRole($role, $ressource = null, $privilege = self::DEFAULT_PRIVILEGE) {

		if(!array_key_exists($role, $this->_roles))
			$this->_roles[$role] = array();
		
		if($ressource && !array_key_exists($ressource, $this->_roles[$role]))
			$this->_roles[$role][$ressource] = array();
		
		if($ressource && $privilege && !in_array($privilege, $this->_roles[$role][$ressource]))
			array_push($this->_roles[$role][$ressource], $privilege);
		
		return $this;
	
	}
	
	final public function addRessource($ressource, $role = null, $privilege = self::DEFAULT_PRIVILEGE) {

		if(!array_key_exists($ressource, $this->_ressources))
			$this->_ressources[$ressource] = array();
		
		if($role && !array_key_exists($role, $this->_ressources[$ressource]))
			$this->_ressources[$ressource][$role] = array();
		
		if($role && $privilege && !in_array($privilege, $this->_ressources[$ressource][$role]))
			array_push($this->_ressources[$ressource][$role], $privilege);
		
		return $this;
	
	}

}

// Do not clause PHP tags unless it is really necessary