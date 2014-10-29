<?php  defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class FUFValidate {

	public static function email($email) {
		return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',$email);
	}
}

