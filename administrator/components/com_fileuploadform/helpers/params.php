<?php defined('_JEXEC') or die('Restricted access');

abstract class FUFParams {

	public static $params;
	public static function getParams() {
		if (isset(static::$params)) return static::$params;

		JLoader::import('joomla.application.component.helper');

		static::$params = JComponentHelper::getParams('com_fileuploadform');

		return static::$params;
	}

	//~ General settings
	protected static $emails;
	public static function getEmails() {
		if (isset(static::$emails)) return static::$emails;

		$params = static::getParams();

		static::$emails = explode(',',trim($params->get('emails','admin@blackbricksoftware.com')));

		return static::$emails;
	}
	protected static $subject;
	public static function getSubject() {
		if (isset(static::$subject)) return static::$subject;

		$params = static::getParams();

		static::$subject = $params->get('subject','Contact Form');

		return static::$subject;
	}

	//~ Image Settings
	protected static $imagesizes;
	public static function getImageSizes() {
		if (isset(static::$imagesizes)) return static::$imagesizes;

		$params = static::getParams();

		static::$imagesizes = array(
			'width' 		=> $params->get('imagemaxwidth',400)>0 ? $params->get('imagemaxwidth',400) : 400,
			'height'		=> $params->get('imagemaxheight',300)>0 ? $params->get('imagemaxheight',300) : 300,
			'maxwidth'		=> $params->get('imagemaxwidth',0),
			'maxheight'		=> $params->get('imagemaxheight',0),
			'minwidth'		=> $params->get('imageminwidth',400),
			'minheight'		=> $params->get('imageminheight',300),
		);

		return static::$imagesizes;
	}

	protected static $systemusers;
	public static function getSystemUsers() {
		if (isset(static::$systemusers)) return static::$systemusers;

		$db = JFactory::getDBO();
		static::$systemusers = $db->setQuery("SELECT `id`,`email`,`name` FROM `#__users` WHERE `sendEmail`=1")->loadObjectList();

		return static::$systemusers;
	}
}
