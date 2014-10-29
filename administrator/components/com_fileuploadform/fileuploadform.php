<?php if (!defined( '_JEXEC' )) die('Direct Access is not allowed.' );

error_reporting(E_ALL ^ E_NOTICE); ini_set('display_errors',1);
if (!function_exists('pre')) { function pre($var) { return "<pre>".print_r($var,true)."</pre>"; } }

JLoader::discover('FUF',JPATH_COMPONENT_ADMINISTRATOR.'/helpers');

JLoader::import('joomla.html.html');
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');

$lang = JFactory::getLanguage();
$lang->load('com_fileuploadform',JPATH_ADMINISTRATOR,'en-GB',true);
$lang->load('com_fileuploadform',JPATH_ADMINISTRATOR,null,true);

JLoader::import('joomla.database.table');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

JLoader::import('joomla.application.component.controller');
$controller = JControllerLegacy::getInstance('FileUploadForm');

$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

$controller->redirect();
