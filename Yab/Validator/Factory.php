<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Factory
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Factory extends Yab_Validator_Abstract {

	public function _validate($value) {

		$override_errors = $this->has('errors') ? $this->get('errors') : array();
		$validators = $this->has('validators') ? $this->get('validators') : array();

		foreach($validators as $validator => $options) {

			$validator_name = is_numeric($validator) ? $options : $validator;
			$validator_options = is_numeric($validator) ? array() : $options;

			try {
			
				$validator = Yab_Loader::getInstance($validator_name, array(), 'Yab_Validator_Abstract');

			} catch(Yab_Exception $e) {

				$validator = Yab_Loader::getInstance('Yab_Validator_'.$validator_name, array(), 'Yab_Validator_Abstract');

			}
			
			if(!$validator->clear()->populate($validator_options)->validate($value, array_key_exists($validator_name, $override_errors) ? $override_errors[$validator_name] : array()))
				$this->_errors += $validator->getErrors();
		
		}

	}

}

// Do not clause PHP tags unless it is really necessary