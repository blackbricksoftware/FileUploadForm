<?php defined('_JEXEC') or die;

class FUFModelList extends JModelList {

	protected $pks = array();
	protected $fks = array();

	function __construct($config = array()) {

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array_keys($this->getFilterFields());
		}

		parent::__construct($config);
	}

	protected function getPks($pk='id') {

		$store = $this->getStoreId();

		if (isset($this->pks[$store])) return $this->pks[$store];
		$this->pks[$store] = array();

		$items = $this->getItems();

		if (!empty($items)) {
			$this->pks[$store] = array_map(function($i)use($pk){ return $i->$pk; },$items);
			JArrayHelper::toInteger($this->pks[$store]);
		}

		return $this->pks[$store];
	}

	protected function getFks($fk='id') {

		$store = $this->getStoreId();

		if (isset($this->fks[$store])&&isset($this->fks[$store][$fk])) return $this->fks[$store][$fk];
		$this->fks[$store][$fk] = array();

		$items = $this->getItems();

		if (!empty($items)) {
			$this->fks[$store][$fk] = array_unique(array_map(function($i)use($fk){ return $i->$fk; },$items));
			JArrayHelper::toInteger($this->fks[$store][$fk]);
		}

		return $this->fks[$store][$fk];
	}


	public function getFilterFields(){

		return array();
	}
}
