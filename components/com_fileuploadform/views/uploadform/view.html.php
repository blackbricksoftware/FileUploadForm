<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.view');

class FileUploadFormViewUploadForm extends FUFFrontView {

	public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->mparams = $this->state->get('parameters.menu') ?  $this->state->get('parameters.menu') : new JRegistry;

		parent::display($tpl);

	}
}
