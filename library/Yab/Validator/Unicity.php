<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Unicity
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Unicity extends Yab_Validator_Abstract {

	const NOT_UNIQUE = '"$1" already exists "$2" in database';

	public function _validate($value) {

		$value = trim($value);

		$statement = $this->get('statement');

		if(!($statement instanceof Yab_Db_Statement))
			throw new Yab_Exception('statement must be an instance of Yab_Db_Statement');

		$statement->bind('?', $value);

		$count = count($statement);

		$limit = $this->has('limit') ? $this->get('limit', 'Int') - 1 : 0;

		if($limit < $count)
			$this->addError('NOT_UNIQUE', self::NOT_UNIQUE, $value, $count);

	}

}

// Do not clause PHP tags unless it is really necessary