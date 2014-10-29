<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.modellist');
// not done at all
class FileUploadFormModelUploads extends FUFModelList {

	protected $me = array();

	public function getListQuery() {

		$user = JFactory::getUser();

		$query = $this->_db->getQuery(true);

		$query->select('*');
		$query->select('`u`.`id` AS `u_id`');

		$query->from($this->_db->quoteName('#__users') . ' AS `u`');
		$query->innerJoin($this->_db->quoteName('#__user_usergroup_map') . ' AS `ug` ON `u`.`id`=`ug`.`user_id`');
		$query->leftJoin($this->_db->quoteName('#__hb_members') . ' AS `m` ON `u`.`id`=`m_uid`');

		//~ $query->where('`u`.`id`!='.$user->id);
		$query->where('`ug`.`group_id`='.(int)HBParams::getMemberGroup());

		$filter_search = $this->getState('state.filter_search');
		if (!empty($filter_search)) {
			$query->where("
				CONCAT_WS(' ',
					`u`.`name`,
					`u`.`username`,
					`u`.`email`,
					`m_address`,
					`m_city`,
					`m_state`,
					`m_zip`,
					`m_phone`
				) LIKE '%".$this->_db->escape($filter_search)."%'
			");
		}

		$order = $this->getState('list.ordering','u.name');
		$dir = $this->getState('list.direction','asc');
		switch ($order) {
			case 'birthday':
				$orderby = 'm_bmonth '.$dir.', m_bday '.$dir;
				break;
			case 'anniversary':
				$orderby = 'm_amonth '.$dir.', m_aday '.$dir;
				break;
			default:
				$orderby = $order.' '.$dir;
				break;
		}
		$query->order($this->_db->escape($orderby));

		return $query;
	}

	protected function getPks($pk='m_id') {
		return parent::getPks($pk);
	}

	public function getFilterFields() {
		return array(
			'u.name' => 'Name',
			'u.email' => 'Email',
			'u.registerDate' => 'Membership',
			'birthday' => 'Birthday',
			'anniversary' => 'Anniversary',
		);
	}

	protected function populateState($ordering = 'u.name', $direction = 'asc') {

		parent::populateState($ordering,$direction);

		$filter_search = $this->getUserStateFromRequest($this->context.'.state.filter_search','filter_search','','string');
		$this->setState('state.filter_search',$filter_search);
	}

	protected function getStoreId($id = '') {

		$id .= ':' . $this->getState('state.filter_search');

		return parent::getStoreId($id);
	}

	public function save(&$member) {

		$table = $this->getTable('Members','HoneyBeeTable');
		$user = JFactory::getUser();

		$table->load(array('m_uid'=>$member['m_uid']));
		if ($table->m_id>0) {
		} else {
			$member['m_time'] = time();
		}
		$member['m_update'] = time();

		// save this bad boy
		$success = $table->save($member);
		if ($success) {
			$member['m_id'] = $table->m_id;
		} else {
			$this->setError($table->getError());
		}
		$table->reset();

		return $success;
	}
}




