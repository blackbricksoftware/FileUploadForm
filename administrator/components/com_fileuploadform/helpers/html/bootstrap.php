<?php defined('JPATH_PLATFORM') or die;

abstract class FUFHtmlBootstrap {

	protected static $loaded = array();

	public static function framework() {
		JHtml::_('bootstrap.framework');
	}

}
