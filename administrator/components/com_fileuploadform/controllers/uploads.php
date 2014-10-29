<?php defined( '_JEXEC' ) or die( 'Restricted access' );

class FileUploadFormControllerUploads extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		$view = $this->input->get('view','uploads');
		$layout = $this->input->get('layout','');
		$this->setRedirect(JRoute::_('index.php?option=com_fileuploadform'.(!empty($view)?'&view='.$view:'').(!empty($layout)?'&layout='.$layout:''),false));
	}

}
