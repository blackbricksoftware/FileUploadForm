<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.view');

class FUFFrontView extends JViewLegacy {

	public function display($tpl=null) {

		$this->view = $this->getName();
		$this->layout = $this->getLayout();
		$this->link = 'index.php?option=com_fileuploadform&view='.$this->view.($this->layout!='default'?'&layout='.$this->layout:'');

		$this->_prepareDocument();

		parent::display($tpl);
	}

	protected function _prepareDocument() {

		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$menu = $app->getMenu();
		$active = $menu->getActive();
		$title = '';

		if ($active) $title = $active->params->get('page_title',$active->title);

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$doc->setTitle($title);
	}

}
