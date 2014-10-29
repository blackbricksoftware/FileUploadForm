<?php defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::discover('FUF',JPATH_ADMINISTRATOR.'/components/com_fileuploadform/helpers');

function FileUploadFormBuildRoute(&$query) {

	$segments = FUFRoute::build($query);

	return $segments;
}

function FileUploadFormParseRoute($segments) {

	$vars = FUFRoute::parse($segments);

	return $vars;
}
