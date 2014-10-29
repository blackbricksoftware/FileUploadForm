<?php defined('JPATH_PLATFORM') or die;

abstract class FUFHtmlJquery {

	protected static $loaded = array();

	public static function framework() {
		JHtml::_('jquery.framework');
	}

	public static function select2($css = true) {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/select2-3.4.5/select2.min.js');

		if ($css) $doc->addStyleSheet(JURI::root(true).'/media/com_fileuploadform/libraries/select2-3.4.5/select2.css');

		static::$loaded[__METHOD__] = true;
	}

	public static function ui($css = true) {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jquery-ui-1.10.3/ui/minified/jquery-ui.min.js');

		if ($css) $doc->addStyleSheet(JURI::root(true).'/media/com_fileuploadform/libraries/jquery-ui-1.10.3/themes/base/minified/jquery-ui.min.css');

		static::$loaded[__METHOD__] = true;
	}

	public static function cookie($defaults = true) {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jquery-cookie-master/jquery.cookie.js');

		if ($defaults) {
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("
				(function($){
					$.cookie.json = true;
					$.cookie.defaults.expires = ".json_encode(FUFHtml::$cookieExpires).";
					$.cookie.defaults.path = ".json_encode(FUFHtml::$cookiePath).";
				})(jQuery);
			");
		}

		static::$loaded[__METHOD__] = true;
	}

	public static function form() {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jquery.form.min.js');

		static::$loaded[__METHOD__] = true;
	}

	public static function blockui() {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jquery.blockUI.js');

		static::$loaded[__METHOD__] = true;
	}

	public static function validation() {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jquery-validation-1.11.1/dist/jquery.validate.min.js');

		static::$loaded[__METHOD__] = true;
	}

	public static function fileupload($css = true) {

		if (!empty(static::$loaded[__METHOD__])) return;

		static::framework();
		static::ui();

		$doc = JFactory::getDocument();

		$doc->addScript(JURI::root(true).'/media/com_fileuploadform/libraries/jQuery-File-Upload-9.5.4/js/jquery.fileupload.js');

		if ($css) {
			$doc->addStyleSheet(JURI::root(true).'/media/com_fileuploadform/libraries/jQuery-File-Upload-9.5.4/css/jquery.fileupload.css');
		}

		static::$loaded[__METHOD__] = true;
	}
}
