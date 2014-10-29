<?php defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class FUFImage {

	public static $path = 'media/com_latenight/images/uploaded';
	public static $types = array(
		IMAGETYPE_JPEG,
		IMAGETYPE_GIF,
		IMAGETYPE_PNG,
	);

	// wrapper for process image for hotel images
	public static function hotelImage($width = 0, $height = 0, $hotelimage = null) {

		if (is_null($hotelimage)) {
			$path = static::processImage($width, $height);
		} else {
			$path = static::processImage($width, $height, 'hotelimage', $hotelimage->hi_id, $hotelimage->hi_name);
			if ($path===false) $path = static::processImage($width, $height);
		}
		if ($path) {
			return $path;
		} else {
			return false;
		}
	}

	// create the name and path for images
	// call with witdh and height to get specific size -- call w/o to get original image
	public static function makePath($type, $pk, $uploadname, $width = 0, $height = 0) {

		// get folder
		$folder = static::$path;
		$folder .= empty($type) ? '' : '/'.JFile::makeSafe($type);
		$name = static::makeName($pk, $uploadname, $width, $height);

		return $folder.'/'.$name;
	}
	public static function makeName($pk, $name, $width = 0, $height = 0) {
		// start with primary key
		$pk = (int)$pk;
		$filename = ($pk>0 ? $pk.'_' : '');
		// add width if we have one
		if ($width) $filename .= $width;
		$filename .= $width||$height?'x':'';
		// add height if we have one
		if ($height) $filename .= $height;
		$filename .= $width||$height?'_':'';
		// add original upload name
		$filename .= $name;
		// return a safe version of name
		return JFile::makeSafe($filename);
	}

	// return path to image of correct size, creating it if it does not exist
	// if it has trouble finding it, it will return a default image
	public static function processImage($width = 0, $height = 0, $type = '', $pk = 0, $uploadname = 'default.gif') {

		// get what sizes we need
		list($width,$height) = static::getImageSizes($width,$height);

		// check if we have the original image
		$origpath = static::makePath($type, $pk, $uploadname);
		if (!JFile::exists(JPATH_ROOT.'/'.$origpath)) return $pk ? static::processImage($width,$height) : false;

		// check if we have already made the resized image
		$path = static::makePath($type, $pk, $uploadname, $width, $height);
		if (JFile::exists(JPATH_ROOT.'/'.$path)) return $path;

		// check if our folder exists and if not, if we can create it
		$folder = dirname(JPATH_ROOT.'/'.$path);
		if (!JFolder::exists($folder)&&!JFolder::create($folder)) return $pk ? static::processImage($width,$height) : false;

		// create us a resized image
		$LNSimpleImage = new LNSimpleImage(JPATH_ROOT.'/'.$origpath);
		$LNSimpleImage->resizeToFit($width,$height);
		$LNSimpleImage->save(JPATH_ROOT.'/'.$path);

		return $path;
	}

	// quick wrapper for getimagesize
	public static function imageInfo($path) {

		if (!JFile::exists($path)) return false;

		$info = getimagesize($path);
		if (!$info) return false;

		if (!in_array($info[2],static::$types)) return false;

		return array(
			'width' => $info[0],
			'height' => $info[1],
			'type' => $info[2],
			'attrs' => $info[3],
			'bits' => $info['bits'],
			'mime' => $info['mime'],
		);
	}

	// returns back what our width and height need to be bases on config settings
	public static $imagessizes;
	public static function getImageSizes($width = 0, $height = 0) {
		if (!$width&&!$height) {
			$sizes = LNParams::getImageSizes();
			$width = $sizes['width'];
			$height = $sizes['height'];
		}
		return array(
			(int)$width,
			(int)$height,
		);
	}
}
