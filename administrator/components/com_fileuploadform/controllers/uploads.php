<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
// not done at all
class FileUploadFormControllerUploads extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		$view = $this->input->get('view','uploads');
		$layout = $this->input->get('layout','');
		$this->setRedirect(JRoute::_('index.php?option=com_fileuploadform'.(!empty($view)?'&view='.$view:'').(!empty($layout)?'&layout='.$layout:''),false));
	}

	public function save() {

		if (!JSession::checkToken()) {
			$this->setMessage('An error occurred. Please submit again.','error');
			$this->redirect();
		}

		$model = $this->getModel('Members');

		$users = $this->input->post->get('users',array(),'array');
		if (!empty($users)) {
			foreach ($users as $uid => $user) {
				$instance = JFactory::getUser($uid);
				$name = JArrayHelper::getValue($user, 'name', '', 'string');
				if (empty($name)) {
					$user['name'] = $instance->name;
				}
				$username = trim(JArrayHelper::getValue($user, 'username', '', 'string'));
				if (empty($username)) {
					$user['username'] = $instance->username;
				} else {
					JLoader::import('joomla.user.helper');
					$testuserid = JUserHelper::getUserId($username);
					if ($testuserid>0&&$testuserid!=$instance->id) {
						$this->setMessage('Username already exists','error');
						$this->redirect();
					}
				}
				$password = JArrayHelper::getValue($user, 'password', '', 'string');
				$password2 = JArrayHelper::getValue($user, 'password2', '', 'string');
				if (!empty($password)) {
					if (strlen($password)<8) {
						$this->setMessage('Your Password is too short','error');
						$this->redirect();
					}
					if ($password!=$password2) {
						$this->setMessage(JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH'),'error');
						$this->redirect();
					}
				}
				$email = JArrayHelper::getValue($user, 'email', '', 'string');
				if (empty($email)) {
					$user['email'] = $instance->email;
				} else {
					if (!HBValidate::email($email)) {
						$this->setMessage('Email is invalid','error');
						$this->redirect();
					}
				}
				$instance->bind($user);
				if (!$instance->save()) {
					$this->setMessage($instance->getError(),'error');
					$this->redirect();
				}
			}
		}

		$members = $this->input->get('members',array(),'array');
		if (!empty($members)) {
			foreach ($members as $uid => $member) {
				if (!empty($member)) {
					$member['m_uid'] = $uid;
					if (!$model->save($member)) {
						$this->setMessage($model->getError(),'error');
						$this->redirect();
					}
				}
			}
		}

		$this->setMessage('Members Saved');
	}

	public function download() {

		$app = JFactory::getApplication();

		$model = $this->getModel('Members');
		$model->getState();
		$model->setState('list.ordering','u.name');
		$model->setState('list.direction','asc');
		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);
		$members = $model->getItems();

		if (!empty($members)) {

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=Members.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			$fh = fopen("php://output", 'w');

			//~ $cols = array('name','m_address','m_city','m_state','m_zip','email','m_phone','m_bmonth','m_bday','m_amonth','m_aday','m_duespaid');

			$output = array('Name','Address','City','State', 'Zip','Email','Phone','Birthday','Anniversary','Member Since','Dues Paid Through');
			fputcsv($fh,$output);

			foreach ($members as $member) {
				//~ foreach ($member as $key => $field) {
					//~ if (!in_array($key,$allowedsaves)) unset($member[$key]);
				//~ }
				$output = array(
					$member->name,
					$member->m_address,
					$member->m_city,
					$member->m_state,
					$member->m_zip,
					$member->email,
					$member->m_phone,
					$member->m_bmonth>0&&$member->m_bday>0?HBHtml::dateOut(null,'@'.mktime(0, 0, 0, $member->m_bmonth, $member->m_bday, 0),"F j"):'',
					$member->m_amonth>0&&$member->m_aday>0?HBHtml::dateOut(null,'@'.mktime(0, 0, 0, $member->m_amonth, $member->m_aday, 0),"F j"):'',
					HBHtml::dateOut('date',$member->registerDate),
					$member->m_duespaid>0?$member->m_duespaid:'',
				);
				fputcsv($fh,$output);
			}

			fclose($fh);
		} else {
			echo 'No Matching Members';
		}

		$app->close();
	}

	public function import() { exit;

		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$model = $this->getModel('Members');

		$members = array_map(function($i){
			$j = str_getcsv($i);
			$l = array_map(function($k){ return trim(preg_replace('/[^\x{0000}-\x{007F}]/u', '', $k)); },$j);
			return $l;
		},preg_split('/[\r\n]+/',file_get_contents(__DIR__ .'/members.csv')));

		if (!empty($members)) {
			foreach ($members as $member) {

				if (count($member)!=33) continue;

				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__users');
				$query->where('email='.$db->quote($member[9]));
				$id = $db->setQuery($query)->loadResult();

				if (!$id) continue;

				$instance = JFactory::getUser($id);
				$date1 = strtotime($member[19]);
				$date2 = strtotime($member[27]);
				$date3 = strtotime($member[28]);
				if ($date1<$date2) {
					if ($date1<$date3) {
						$instance->registerDate = $member[19];
					} else {
						$instance->registerDate = $member[28];
					}
				} else {
					if ($date2<$date3) {
						$instance->registerDate = $member[27];
					} else {
						$instance->registerDate = $member[28];
					}
				}
				//~ echo pre($instance);
				$instance->bind($user);
				if (!$instance->save()) echo pre($instance->getError());

				$a = array(
					'm_uid' => $id,
					'm_address' => $member[3],
					'm_city' => $member[4],
					'm_state' => strlen($member[5])==2?strtoupper($member[5]):'',
					'm_zip' => $member[6],
					'm_phone' => $member[7],
					'm_bmonth' => $member[29],
					'm_bday' => $member[30],
					'm_amonth' => $member[31],
					'm_aday' => $member[32],
					'm_duespaid' => '',
				);
				//~ echo pre($a);

				if (!$model->save($a)) echo pre($model->getError());
			}
		}

		$app->close();
	}
}
