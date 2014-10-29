<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.controller');

class FileUploadFormController extends JControllerLegacy {

	public function display($cachable = false, $urlparams = false) {

		$view = $this->input->get('view', 'uploads', 'cmd');
		$this->input->set('view',$view);

		return parent::display($cachable, $urlparams);
	}

}
