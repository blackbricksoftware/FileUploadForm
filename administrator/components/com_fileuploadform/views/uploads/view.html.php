<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.view');

class FileUploadFormViewUploads extends FUFBackView {

	public function display($tpl = null) {

		//~ $bar = JToolbar::getInstance('toolbar');
//~
		//~ $this->state = $this->get('State'); //echo  pre($this->state);
		//~ $this->members = $this->get('Items'); // echo pre($this->getModel()->getDBO());
		//~ $this->total = $this->get('Total');
		//~ $this->pagination = $this->get('Pagination');
		//~ $this->filterfields = $this->get('FilterFields');
		//~
		//~ $this->states = HBParams::getStates();
//~
		//~ JToolbarHelper::apply('members.save');
		//~ $bar->appendbutton('Custom','
			//~ <a class="btn btn-small" target="_blank" href="'.JRoute::_('index.php?option=com_honeybee&task=members.download').'"><i class="icon-download"></i> Download</a>
		//~ ');
		//~ $this->addPublishButtons(false);

		parent::display($tpl);
	}
}
