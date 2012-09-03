<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Upload
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Upload extends Yab_Validator_Abstract {

	const BAD_DESTINATION = '"$1" is not a valid writable destination dircectory';
	const BAD_VALUE = '"$1" parameters is not present in $_FILES';
	const BAD_FILE_NAME = '"$1" is not a valid file name';
	const NO_TMP_FILE = '"$1" does not exists as a tmp file';
	const NO_OVERRIDE = '"$1" already exists on the server and can not be override';
	const NO_RENAME = '"$1" can not be renamed into "$2", check permissions';

	public function _validate($value) {

		$override = $this->get('override', 'Bool');
		$destination = $this->get('destination');
		$mimes = $this->has('mimes') ? $this->get('mimes', 'Array') : array();
		$size = $this->has('size') ? $this->get('size', 'Int') : 0;

		if(!is_dir($destination) || !is_writable($destination))
			return $this->addError('BAD_DESTINATION', self::BAD_DESTINATION, $destination);

		foreach(array('name', 'type', 'tmp_name', 'size') as $var)
			if(!is_array($value) || !array_key_exists($var, $value) || !$value[$var])
				return $this->addError('BAD_VALUE', self::BAD_VALUE, $var);

		$value['name'] = $this->has('name') ? $this->get('name') : $value['name'];

		if($this->has('rewrite')) {

			$delimiter = '-';

			$value['name'] = preg_replace("/&(.)(grave|acute|cedil|circ|ring|tilde|uml);/", '\\1', strtolower(htmlentities($value['name'], ENT_QUOTES, 'UTF-8')));
			$value['name'] = preg_replace("/([^a-z0-9\.]+)/", $delimiter, html_entity_decode($value['name']));

			while(is_numeric(strpos($value['name'], $delimiter.$delimiter)))
				$value['name'] = str_replace($delimiter.$delimiter, $delimiter, $value['name']);

			$value['name'] = trim($value['name'], $delimiter);

		}

		if(preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $value['name']))
			return $this->addError('BAD_FILE_NAME', self::BAD_FILE_NAME, $value['name']);

		if(!is_file($value['tmp_name']) || !is_uploaded_file($value['tmp_name']))
			return $this->addError('NO_TMP_FILE', self::NO_TMP_FILE, $value['tmp_name']);

		if(count($mimes) && !in_array($value['type'], $mimes))
			return false;

		if($size && $size < $value['size'])
			return false;

		$destination = $destination.DIRECTORY_SEPARATOR.$value['name'];

		if(!$override && is_file($destination))
			return $this->addError('NO_OVERRIDE', self::NO_OVERRIDE, $destination);

		if(!move_uploaded_file($value['tmp_name'], $destination))
			return $this->addError('NO_RENAME', self::NO_RENAME, $value['tmp_name'], $destination);

	}

}

// Do not clause PHP tags unless it is really necessary