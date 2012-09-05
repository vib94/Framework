<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Existence
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Existence extends Yab_Validator_Abstract {

	const NOT_EXISTS = '"$1" doesn\'t exists in database';

	public function _validate($value) {

		$value = trim($value);

		$statement = $this->get('statement');

		if(!($statement instanceof Yab_Db_Statement))
			throw new Yab_Exception('statement must be an instance of Yab_Db_Statement');

		$statement->bind('?', $value);

		if(!count($statement))
			$this->addError('NOT_EXISTS', self::NOT_EXISTS, $value);

	}

}

// Do not clause PHP tags unless it is really necessary