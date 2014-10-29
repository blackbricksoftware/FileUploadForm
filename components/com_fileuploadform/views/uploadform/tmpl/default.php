<?php defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('fufhtml.jquery.validation');
JHtml::_('fufhtml.jquery.ui');
JHtml::_('fufhtml.jquery.fileupload');

if ($this->mparams->get('show_page_heading', 1)) { ?>
	<h1><?= $this->escape($this->mparams->get('page_heading')) ?></h1>
<?php } ?>
<form action="<?= JRoute::_($this->link) ?>" method="post" target="_self" class="form-horizontal" id="FUFUploadForm">
	<div class="row-fluid">
		<div class="span6">
			 <div class="control-group">
				<label class="control-label" for="FUFUploadForm[company]">Company</label>
				<div class="controls">
					<input type="text" id="FUFCompany" name="FUFUploadForm[company]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[name]">Name</label>
				<div class="controls">
					<input type="text" id="FUFName" name="FUFUploadForm[name]" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[email]">Email</label>
				<div class="controls">
					<input type="email" id="FUFEmail" name="FUFUploadForm[email]" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[phone]">Phone</label>
				<div class="controls">
					<input type="text" id="FUFPhone" name="FUFUploadForm[phone]" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[how]">How did you hear about us?</label>
				<div class="controls">
					<input type="text" id="FUFHow" name="FUFUploadForm[how]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[completion]">Desired Completion Date</label>
				<div class="controls">
					<input type="text" id="FUFCompletion" name="FUFUploadForm[completion]" readonly>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="control-group">
				<label class="control-label" for="FUFUploadForm[message]">Message / Project Description</label>
				<div class="controls">
					<textarea id="FUFMessage" name="FUFUploadForm[message]" rows="3" style="height: auto;"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="FUFFiles">Files</label>
				<div class="controls">
					<p>Upload files here, then submit form.</p>
					 <!-- The fileinput-button span is used to style the file input field as button -->
					<span class="btn fileinput-button">
						<i class="icon-plus"></i>&nbsp;&nbsp;
						<span>Select files...</span>
						<!-- The file input field used as target for the file upload widget -->
						<input id="FUFFilesUpload" type="file" name="FUFFilesUpload[]" multiple>
					</span>
					<br>
					<br>
					<!-- The global progress bar -->
					<div id="FUFFilesProgress" class="progress">
						<div class="bar"></div>
					</div>
					<!-- The container for the uploaded files -->
					<div id="FUFFiles" class="files"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions" style="margin-top: 0; padding-left: 0; text-align: center;">
		<?php
		$captcha = JCaptcha::getInstance(JFactory::getConfig()->get('captcha'));
		if ($captcha) echo $captcha->display('FUFUploadForm[captcha]','FUFCaptcha','input-mini required');
		?>
		<br><br>
		<input type="submit" id="FUFSubmit" name="FUFSubmit" value="Submit" class="btn btn-large">
	</div>
	<input type="hidden" name="task" value="uploadform.save" />
	<input type="hidden" name="option" value="com_fileuploadform" />
	<input type="hidden" name="view" value="<?= $this->escape($this->view) ?>" />
	<input type="hidden" name="layout" value="<?= $this->escape($this->layout) ?>" />
	<?=  JHtml::_('form.token') ?>
</form>
<script type="text/javascript">
	jQuery(function($){

		$('#FUFUploadForm').validate({
			ignore: null,
			errorClass: 'text-error',
		});
		$('#FUFUploadForm').data('autosubmit',false).submit(function(){
			if (!$(this).valid()) return;
			if ($('#FUFFilesUpload').data('uploading')) {
				$(this).data('autosubmit',true).prepend(
					$('<div />').addClass('alert alert-warning').html(
						'Files are still uploading. Form will autosubmit once upload is complete.'
					)
				);
				return false;
			}
			return true;
		});

		$('#FUFCompletion').datepicker();

		// Change this to the location of your server-side upload handler
		$('#FUFFilesUpload').data('uploading',false).fileupload({
			url: <?= json_encode(JURI::base(true).'/index.php?option=com_fileuploadform&task=uploadform.fileSave') ?>,
			dataType: 'json',
			formData: {
				<?= json_encode(JSession::getFormToken()) ?>: '1',
			},
			start: function(e){
				$(this).data('uploading',true);
			},
			// {"FUFFilesUpload":[{"name":"page2_signed.pdf","size":175883,"type":"x-unknown\/x-unknown","error":"Filetype not allowed"}]}
			// {"FUFFilesUpload":[{"name":"page2_signed.pdf","size":175883,"type":"x-unknown\/x-unknown","url":"\/components\/com_fileuploadform\/files\/page2_signed.pdf","deleteUrl":"\/index.php?option=com_fileuploadform&task=uploadform.fileSave&FUFFilesUploa=page2_signed.pdf","deleteType":"DELETE"}]}
			done: function (e, data) { // console.log(e); console.log(data);
				$.each(data.result.FUFFilesUpload, function (index, file) { // console.log(file);
					if ('error' in file) {
						var text = '<strong>Upload Failure:</strong> '+file.name+' ('+file.error+')';
						var textClass = 'text-error';
					} else {
						var text = '<strong>Upload Success:</strong> '+file.name
						var textClass = 'text-success';
						$('#FUFUploadForm').append(
							$('<input />').attr('type','hidden').attr('name','FUFFilesUpload[]').val(JSON.stringify(file))
						);
					}
					$('<p/>').addClass(textClass).html(text).appendTo('#FUFFiles');
				});

				var form = $('#FUFUploadForm');
				if (!form.data('autosubmit')||$(this).data('uploading')) return;
				form.find('.alert').remove();
				form.prepend(
					$('<div />').addClass('alert alert-success').html(
						'Submitting....'
					).prepend($('<button />').attr('type','button').addClass('close').html('&times;'))
				).submit();
			},
			progressall: function (e, data) { // console.log(e); console.log(data);
				var progress = parseInt(data.loaded / data.total * 100, 10); // console.log(data.loaded); console.log(data.total); console.log(progress);
				$('#FUFFilesProgress .bar').css('width', progress + '%');
				if (progress>=100) $(this).data('uploading',false);
			}
		}).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

	});
</script>
