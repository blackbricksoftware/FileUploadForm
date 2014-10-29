<?php defined( '_JEXEC' ) or die( 'Restricted access' );

// slight variation on JRoute to allow to specify base so that we can get site route from admin
class FUFRoute {

	private static $_instance;
	public static $defaultView = 'uploadform';

	public $menuitems;

	function __construct() {

		$db = JFactory::getDBO();

		// find our menu items so we route to them -- store statically so we only have to run this query once -- organize by view and layout
		if (is_null($this->menuitems)) {
			$this->menuitems = array();
			$raws = $db->setQuery("
				SELECT *
				FROM `#__menu`
				WHERE 1
					AND `link` LIKE 'index.php?option=com_fileuploadform%'
					AND client_id=0
					AND published=1
				GROUP BY `link`
				ORDER BY `id` ASC
			")->loadObjectList();
			if (count($raws)>0) {
				foreach ($raws as $raw) {
					parse_str($raw->link,$rawquery);
					if (!array_key_exists('layout',$rawquery)) $rawquery['layout'] = 'default';
					$this->menuitems[$rawquery['view']][$rawquery['layout']] = $raw;
				}
			}
		}
	}

	public static function getInstance() {

		if (empty(static::$_instance)) {
			static::$_instance = new FUFRoute();
		}

		return static::$_instance;
	}

	public function build(&$query) {

		$segments = array();

		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$menu = $app->getMenu();
		$active = $menu->getActive();
		$default = $menu->getDefault();
		$h = FUFRoute::getInstance();

		if (
			$h->isMenuItem($query) ||
			count($h->menuitems)<=0
		) return $segments;

		$unsets = array();

		// find a view and a layout
		list($view,$layout) = $h->getViewLayout($query);

		if (array_key_exists($view,$h->menuitems)) {
			if (array_key_exists($layout,$h->menuitems[$view])) {
				$query['Itemid'] = $h->menuitems[$view][$layout]->id;
				$unsets['view'] = $unsets['layout'] = true;
			} else {
				$query['Itemid'] = reset($h->menuitems[$view])->id;
				$unsets['view'] = true;
			}
		}
		$query['Itemid'] = $query['Itemid']>0 ? $query['Itemid'] : $active->id;

		//~ switch ($view) {
			//~ case 'projects':
				//~ if ($layout=='project'&&$h->akegz('p_id',$query)) {
					//~ $segments[] = $query['p_id'];
					//~ $unsets['layout'] = $unsets['p_id'] = true;
				//~ }
				//~ break;
			//~ case 'jobs':
				//~ if ($layout=='job'&&$h->akegz('j_id',$query)) {
					//~ $segments[] = $query['j_id'];
					//~ $unsets['layout'] = $unsets['j_id'] = true;
				//~ }
				//~ break;
		//~ }

		$h->unsets($query,$unsets);

		return $segments;


	}

	public function parse() {

		$vars = array();

		//~ $db = JFactory::getDBO();
		//~ $menu = JSite::getMenu();
		//~ $active = $menu->getActive();
		//~ $default = $menu->getDefault();
		//~ $h = HBRoute::getInstance();
	//~
		//~ $Itemid = JRequest::getInt('Itemid');
		//~ if ($Itemid<=0) return $vars;
	//~
		//~ $item = $menu->getItem($Itemid);
		//~ parse_str($item->link,$query);
		//~ list($view,$layout) = $h->getViewLayout($query,$segments[0]);
		//~ $vars['view'] = $view;
		//~ $vars['layout'] = $layout;
	//~
		//~ if (count($segments)>0 && $segments[0]>0) {
			//~ switch ($view) {
				//~ case 'projects':
					//~ $vars['layout'] = 'project';
					//~ $vars['p_id'] = $segments[0];
					//~ break;
				//~ case 'jobs':
					//~ $vars['layout'] = 'job';
					//~ $vars['j_id'] = $segments[0];
					//~ break;
			//~ }
		//~ }
	//~
		//~ echo "<pre>",print_r($vars,true),"</pre>";

		return $vars;

	}
	public function isMenuItem(&$query) {

		// regular menu items routing -- joomla will figure it out
		return
			$this->akegz('Itemid',$query) &&
			!array_key_exists('view',$query) &&
			!array_key_exists('layout',$query);

	}

	public function akegz($key,&$query) {
		return
			array_key_exists($key,$query) &&
			$query[$key]>0;
	}

	public function getViewLayout(&$query) {

		if (array_key_exists('view',$query)&&array_key_exists('layout',$query)) {
			$view = $query['view'];
			$layout = $query['layout'];
		} elseif (array_key_exists('view',$query)) {
			$view = $query['view'];
			$layout = 'default';
		} elseif (array_key_exists('layout',$query)) {
			$view = $layout = $query['layout'];
		} else {
			$view = $layout = static::$defaultView;
		}

		return array($view,$layout);
	}

	public function unsets(&$query,$unsets) {
		if (count($unsets)>0) {
			foreach ($unsets as $var => $unset) {
				if (!$unset) continue;
				if (array_key_exists($var,$query)) unset($query[$var]);
			}
		}
	}

	public static function site($url, $full = false) {
		$siterouter = JApplication::getInstance('site')->getRouter();
		$newurl = $siterouter->build($url)->toString();
		$return = preg_replace('#^'.preg_quote(JUri::base(true).'/').'#','',$newurl);
		return $full ? JUri::root().$return : JUri::root(true).'/'.$return;
	}
}
