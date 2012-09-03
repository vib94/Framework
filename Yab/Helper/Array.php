<?php
/**
 * Yab Framework
 *  
 * @category   Yab_Helper
 * @package    Yab_Helper_Array
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Helper_Array {

	protected $_datas = array();

	public function __construct(array $datas) {

		$this->_datas = $datas;

	}

	public function group(array $group_keys) {

		$datas = array();
		
		$group_indexes = array();
		
		foreach($this->_datas as $data_key => $data_value) {
		
			$group_index = '';
		
			foreach($group_keys as $group_key) {
			
				if(!array_key_exists($group_key, $data_value))
					throw new Yab_Exception('"'.$group_key.'" is not a valid group_key in datas array');
			
				$group_index .= $data_value[$group_key];
			
			}
			
			$group_index = md5($group_index);
			
			if(!array_key_exists($group_index, $group_indexes)) {
			
				$group_indexes[$group_index] = $data_key;

				$datas[$data_key] = $data_value;
				
			} else {
			
				$data_key = $group_indexes[$group_index];
			
				foreach($datas[$data_key] as $key => $value) {
				
					if($data_value[$key] == $value)
						continue;
				
					if(!is_array($value))
						$datas[$data_key][$key] = array($value);
						
					array_push($datas[$data_key][$key], $data_value[$key]);
				
				}
	
			}

		}
		
		return $datas;
	
	}
	
}

// Do not clause PHP tags unless it is really necessary