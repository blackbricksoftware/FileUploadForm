<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class FileUploadFormControllerUploadForm extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		$view = $this->input->get('view','uploadform');
		$layout = $this->input->get('layout','');
		$this->setRedirect(JRoute::_('index.php?option=com_fileuploadform'.(!empty($view)?'&view='.$view:'').(!empty($layout)?'&layout='.$layout:''),false));
	}

	public function save() {

		if (!JSession::checkToken()) {
			$this->setMessage('An error occurred. Please submit again.','error');
			$this->redirect();
		}

		$FUFUploadForm = $this->input->post->get('FUFUploadForm',array(),'array');

		$captcharesponse = JArrayHelper::getValue($FUFUploadForm, 'captcha', null ,null);
		$captcha = JCaptcha::getInstance(JFactory::getConfig()->get('captcha'));
		if ($captcha&&!$captcha->checkAnswer($captcharesponse)) {
			$vars = JFactory::getSession()->get('mathcaptcha');
			$this->redirect();
		}

		$toEmails = FUFParams::getEmails();
		if (empty($toEmails)) {
			$this->setMessage('An error occurred.','error');
			$this->redirect();
		}

		$name = JArrayHelper::getValue($FUFUploadForm, 'name', '', 'string');
		if (empty($name)) {
			$this->setMessage('Please enter a name','error');
			$this->redirect();
		}

		$email = JArrayHelper::getValue($FUFUploadForm, 'email', '', 'string');
		if (!FUFValidate::email($email)) {
			$this->setMessage('Please enter a valid email','error');
			$this->redirect();
		}

		$phone = JArrayHelper::getValue($FUFUploadForm, 'phone', '', 'string');
		if (empty($phone)) {
			$this->setMessage('Please enter a phone','error');
			$this->redirect();
		}

		$FUFFilesUpload = array_filter($this->input->post->get('FUFFilesUpload',array(),'array'));

		JLoader::import('cms.html.number');
		$mail = JFactory::getMailer();
		$mail->IsHTML();
		$mail->addReplyTo($email,$name);
		foreach ($toEmails as $toEmail) $mail->addRecipient($toEmail);
		$mail->setSubject(FUFParams::getSubject());
		ob_start(); ?>
			<b>Company:</b> <?= htmlspecialchars(JArrayHelper::getValue($FUFUploadForm, 'company', '', 'string')) ?><br />
			<b>Name:</b></u> <?= htmlspecialchars($name) ?><br />
			<b>Email:</b></u> <?= htmlspecialchars($email) ?><br />
			<b>Phone:</b></u> <?= htmlspecialchars($phone) ?><br />
			<b>How they heard about CILE:</b></u> <?= htmlspecialchars(JArrayHelper::getValue($FUFUploadForm, 'how', '', 'string')) ?><br />
			<b>Desired Completion Date:</b></u> <?= htmlspecialchars(JArrayHelper::getValue($FUFUploadForm, 'completion', '', 'string')) ?><br />
			<u><b>Message:</b></u><br /><?= htmlspecialchars(JArrayHelper::getValue($FUFUploadForm, 'message', '', 'string')) ?><br />
			<?php if (!empty($FUFFilesUpload)) { ?>
				<u><b>Files:</b></u><br />
				<?php foreach ($FUFFilesUpload as $key => $val) {
					$file = json_decode($val); ?>
					<a href="<?= htmlspecialchars($file->url) ?>" target="_blank">
						<?= htmlspecialchars($file->name) ?>
					</a> - <?= htmlspecialchars(JHtmlNumber::bytes($file->size)) ?><br />
				<?php } ?>
			<?php } else { ?>
				<u><b>No Files Uploaded</b></u>
			<?php } ?>
		<?php $mail->setBody(ob_get_clean());
		$mail->Send();

		$this->setMessage('Thank you for your inquiry. A customer service representative will respond to your shortly.');
	}

	public function fileSave() {

		$app = JFactory::getApplication();

		//~ if (!JSession::checkToken()) $app->close(); // cant access if directly linked :(

		JLoader::import('joomla.filesystem.folder');

		$options = array(
			'script_url' => JURI::base().'index.php?option=com_fileuploadform&task=uploadform.fileSave',
            'upload_dir' => JPATH_COMPONENT . '/uploads/',
            'upload_url' => JURI::base().'components/com_fileuploadform/uploads/',
			'param_name' => 'FUFFilesUpload',
			//~ 'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
			'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
			'accept_file_types' => '/\.(gif|jpe?g|png|bmp|tiff|psd|xcf|wma|wmv|mpg3|mpg|mpeg|avi|mov|ogv|pdf)$/i',
			'image_file_types' => '/$.^/',
			'download_via_php' => 1,
		);

		$FUFUploadHandler = new FUFUploadHandler($options);

		$app->close();
	}
}
