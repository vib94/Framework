<?php
/**
 * Yab Framework
 *  
 * @category   Yab_File
 * @package    Yab_File_Csv
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_File_Csv extends Yab_File {

	private $_field_separator = ';';
	private $_enclosure = '"';
	
	private $_callbacks = array();
	private $_datas = array();
	private $_fields = array();

	final public function setFieldSeparator($field_separator) {

		$this->_field_separator = $field_separator;

		return $this;

	}

	final public function setEnclosure($enclosure) {

		$this->_enclosure = $enclosure;

		return $this;

	}

	final public function setDatas($datas) {

		$this->_datas = $datas;

		return $this;

	}

	final public function setFields(array $fields) {

		$this->_fields = $fields;

		return $this;

	}

	final public function getDatas() {

		return $this->_datas;

	}

	final public function getFields() {

		return count($this->_fields) ? $this->_fields : $this->extractFields();

	}

	final public function extractFields() {

		$fields = array();

		foreach($this->_datas as $data) {

			foreach($data as $key => $value)
				$fields[$key] = $key;

			break;

		}

		return $fields;

	}
	
	protected function getContent() {

		if($this->_content !== null)
			return $this->_content;

		$datas = $this->getDatas();
		$fields = $this->getFields();

		$this->_content = $this->line($fields);
		
		foreach($datas as $data) {

			$values = array();

			foreach($fields as $key => $value) {

				if(is_numeric($key))
					$key = $value;

				$values[$key] = $this->_callback($data, $key);

			}

			$this->_content .= $this->line($values);

		}

		$this->_content = $this->encode($this->_content);
		$this->_content = $this->convert($this->_content);

		return $this->_content;

	}

	protected function _callback($data, $field) {

		if(!array_key_exists($field, $this->_callbacks))
			return $data[$field];
		
		$callback = $this->_callbacks[$field];
		
		$object = array_shift($callback);
		$method = array_shift($callback);

		return $object->$method($data, $field);
		
	}
	
	public function addCallback($field, $object, $method) {
	
		$this->_callbacks[$field] = array($object, (string) $method);
		
		return $this;
	
	}
	
	public function remCallback($field) {
	
		unset($this->_callbacks[$field]);
		
		return $this;
	
	}
	
	public function line($datas) {

		$line = '';
		$escape_char = '\\';

		foreach($datas as $key => $value) {

			$value = is_array($value) ? implode(', ', $value) : $value;
		
			if(
				strpos($value, $this->_field_separator) !== false || 
				strpos($value, $this->_enclosure) !== false || 
				strpos($value, "\n") !== false || 
				strpos($value, "\r") !== false || 
				strpos($value, "\t") !== false || 
				strpos($value, ' ') !== false
			) {

				$str2 = $this->_enclosure;
				$escaped = 0;
				$len = strlen($value);

				for($i = 0; $i < $len; $i++) {
				
					if($value[$i] == $escape_char)
						$escaped = 1;
					elseif(!$escaped && $value[$i] == $this->_enclosure)
						$str2 .= $this->_enclosure;
					else
						$escaped = 0;
						
					$str2 .= $value[$i];
					
				}
				
				$str2 .= $this->_enclosure;
				$line .= $str2.$this->_field_separator;
				
			} else {
				
				$line .= $value.$this->_field_separator;
				
			}

		}

		$line = substr($line, 0, -1);
		$line .= $this->getLineEnding();
		
		return $line;

	}

}

// Do not clause PHP tags unless it is really necessary