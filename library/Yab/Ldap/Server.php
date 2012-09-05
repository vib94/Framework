<?php
/**
 * Yab Framework
 *
 * @category   Yab_Ldap
 * @package    Yab_Ldap_Server
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Ldap_Server {

	private $_host = null;
	private $_login = null;
	private $_password = null;
	private $_port = null;

	private $_resource = null;

	public function __construct($host, $login, $password, $port = 389) {

		$this->_host = (string) $host;
		$this->_login = (string) $login;
		$this->_password = (string) $password;
		$this->_port = intval($port);

	}

	public function __destruct() {

		$this->disconnect();

	}

	protected function connect() {

		if(is_resource($this->_resource))
			return $this;

		$this->_resource = ldap_connect($this->_host, $this->_port);

		if(!is_resource($this->_resource))
			throw new Yab_Exception($this->_host.':'.$this->_port.' is not a valid sspi configuration');

		ldap_set_option($this->_resource, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_resource, LDAP_OPT_REFERRALS, 0);

		if(!ldap_bind($this->_resource, $this->_login, $this->_password))
			throw new Yab_Exception('login/password are not valid credentials for ldap server');

		return $this;

	}

	public function disconnect() {

		if(is_resource($this->_resource))
			ldap_close($this->_resource);

		return $this;

	}

	public function search($filter) {

		$this->connect();

		if(!preg_match('#^.+\.([a-z\-]+)\.([a-z]+)$#i', $this->_host, $match))
			throw new Yab_Exception($this->_host.' does not have a domain and domain extension');

		$search = ldap_search($this->_resource, "dc=".$match[1].",dc=".$match[2], $filter);

		if(!is_resource($search))
			throw new Yab_Exception($login.' not found '.$filter.' on "dc='.$match[1].',dc='.$match[2].'"');

		$infos = ldap_get_entries($this->_resource, $search);

		if(!is_array($infos))
			return array('count' => 0);

		return $infos;

	}

}

// Do not clause PHP tags unless it is really necessary