<?php  defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class FUFHtml {

	// time defaults
	public static $dateFormat = 'M j, y';
	public static $timeFormat = 'g:i a';

	// date/timepicker format defaults
	public static $datpickerFormat = 'M d, y';
	public static $timepickerFormat = 'h:mm tt';

	// cookie defaults
	public static $locationCookieName = 'FUFGeoRecord';
	public static $cookieExpires = 365;
	public static $cookiePath = '/';

	/*******************************************************************************
	**** Wrapper for JHTML::_('date'.... to auto format
	*
	* ---- Output formatted date: ----
	* static::dateOut('date',$time);
	*
	*******************************************************************************/

	public static function dateOut($type = 'date', $date = 'now', $format = null, $tz = true, $gregorian = false, $filter_null = true) {

		if (is_null($date)||$date=='@') return '';

		if ($filter_null&&is_null($date)) return '';

		switch ($type) {
			case 'both':
				$format = static::$dateFormat.' '.static::$timeFormat;
				break;
			case 'time':
				$format = static::$timeFormat;
				break;
			case 'date':
			default:
				$format = static::$dateFormat;
				break;
		}

		return JHTML::_('date',$date,$format);
	}

	/*******************************************************************************
	**** Wrapper for JFactory::getDate to auto account for timezone -- probably used with ->toUnix()
	*
	* ---- Output formatted date: ----
	* static::dateIn($datestring,$timezone);
	*
	*******************************************************************************/
	public static function dateIn($date,$timezone=null) {

		if (is_null($timezone)) {
			$config = JFactory::getConfig();
			$user = JFactory::getUser();
			$timezone = $user->getParam('timezone',$config->get('offset'));
		}

		return  JFactory::getDate($date,$timezone);
	}

	/*******************************************************************************
	**** Wrapper for JHtmlDate::relative
	* @param   string  $date  The date to convert
	* @param   string  $unit  The optional unit of measurement to return
	*                         if the value of the diff is greater than one
	* @param   string  $time  An optional time to compare to, defaults to now
	*
	* @return  string  The converted time string
	*******************************************************************************/
	public static function relativeDate($date, $unit = null, $time = null) {

		if (is_null($date)||$date=='@') return '';

		if (is_null($time)||$time=='@') $time = JFactory::getDate('now');

		return JHtmlDate::relative($date, $unit, $time);
	}

	/*******************************************************************************
	**** Find and use some timezone like joomla does, based on JFormFieldTimezone getGroups
	*******************************************************************************/
	protected static $zones = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
	protected static $timezones = null;
	public static function getTimezoneList() {

		if (!is_null(static::$timezones)) return static::$timezones;

		if (is_null($selected)) {
			$config = JFactory::getConfig();
			$user = JFactory::getUser();
			$selected = $user->getParam('timezone',$config->get('offset'));
		}

		static::$timezones = array();

		// Get the list of time zones from the server.
		$zones = DateTimeZone::listIdentifiers();
		//~ echo pre($zones);

		// Build the group lists.
		foreach ($zones as $zone) {

			// Time zones not in a group we will ignore.
			if (strpos($zone, '/') === false) continue;

			// Get the group/locale from the timezone.
			list ($group, $locale) = explode('/', $zone, 2);
			//~ echo pre($zone),pre($group),pre($locale);

			// Only use known groups.
			if (in_array($group, static::$zones)) {

				// Initialize the group if necessary.
				if (!isset(static::$timezones[$group]))  static::$timezones[$group] = array();

				// Only add options where a locale exists.
				if (!empty($locale)) static::$timezones[$group][$zone] = str_replace('_', ' ', $locale);

			}
		}
		//~ echo pre(static::$timezones);

		// Sort the group lists.
		ksort(static::$timezones);
		foreach (static::$timezones as &$location) ksort($location);

		return static::$timezones;
	}

	public static function getTimezoneOptions($selected = null, $blank = true) {

		$timezones = static::getTimezoneList();

		if (empty($timezones)) return '';

		$timezoneoptions = '';
		if ($blank) $timezoneoptions .= '<option value="" '.(empty($selected)?'selected':'').'></option>';
		foreach ($timezones as $group => $zones) {
			if (empty($zones)) continue;
			$timezoneoptions .= '<optgroup label="'.htmlspecialchars($group).'">';
			foreach ($zones as $zone => $text) {
				$timezoneoptions .= '<option value="'.htmlspecialchars($zone).'" '.(!is_null($selected)&&$selected==$zone?'selected':'').'>'.htmlspecialchars($text).'</option>';
			}
			$timezoneoptions .= '</optgroup>';
		}

		return $timezoneoptions;
	}

	public static function getPublishedState($state) {

		$state = (int)$state;

		switch ($state) {
			case 0: // unpublished
				return array('info','Unpublished');
				break;
			case 2: // archived
				return array('warning','Archived');
				break;
			case -2: // trashed
				return array('error','Trashed');
				break;
			case 1: // published
				return array('success','Published');
				break;
			default:
				return '';
				break;
		}
	}

	public static function getPublishedStateActive($state) {

		$state = (int)$state;

		switch ($state) {
			case 0: // unpublished
				return array('info','Inactive');
				break;
			case 2: // archived
				return array('warning','Archived');
				break;
			case -2: // trashed
				return array('error','Trashed');
				break;
			case 1: // published
				return array('success','Active');
				break;
			default:
				return '';
				break;
		}
	}

	public static function getPublishedStateCheckedIn($state) {

		$state = (int)$state;

		switch ($state) {
			case 0: // unpublished
				return array('info','Not Checked-In');
				break;
			case 2: // archived
				return array('warning','Checked-In');
				break;
			case -2: // trashed
				return array('error','Cancelled');
				break;
			case 1: // published
				return array('success','Published');
				break;
			default:
				return '';
				break;
		}
	}

	public static function getPublishedStatePayment($state) {

		$state = (int)$state;

		switch ($state) {
			case 0:
				return array('info','Not Paid');
				break;
			case 1:
				return array('success','Paid');
				break;
			default:
				return '';
				break;
		}
	}

	public static function getPublishedLabel($state,$alt=false) {

		if ($alt) {
			list($class,$label) = call_user_func('static::getPublishedState'.$alt,$state);
		} else {
			list($class,$label) = static::getPublishedState($state);
		}

		return '<span class="label label-'.$class.'">'.$label.'</span>';
	}
}

