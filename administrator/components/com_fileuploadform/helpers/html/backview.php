<?php defined('JPATH_PLATFORM') or die;

abstract class FUFHtmlBackView {

	protected static $loaded = array();

	public static function newitem($selector = '#newItem') {

		if (!empty(static::$loaded[__METHOD__])) return;

		$doc = JFactory::getDocument();

		JHtml::_('fufhtml.bootstrap.framework');
		ob_start(); ?>
		<script type="text/javascript">
			jQuery(function($){
				$(<?= json_encode($selector) ?>).on('show',function(){
					$('.item_create',this).val('1');
					$('.needsRequired',this).prop('required',true);
				}).on('hidden',function(){
					$('.item_create',this).val('0');
					$('.needsRequired',this).prop('required',false);
				});
			});

		</script>
		<?php $doc->addCustomTag(ob_get_clean());

		static::$loaded[__METHOD__] = true;
	}

	public static function validation($selector = '#adminForm') {

		if (!empty(static::$loaded[__METHOD__])) return;

		$doc = JFactory::getDocument();

		JHtml::_('fufhtml.jquery.validation');
		ob_start(); ?>
		<script type="text/javascript">
			jQuery(function($){

				// original for reference
				//~ Joomla.submitbutton = function (a) {
					//~ Joomla.submitform(a)
				//~ };

				var obj = $(<?= json_encode($selector) ?>);
				obj.validate({
					ignore: '.ignoreValidation',
					errorClass: 'text-error',
				});
				Joomla.submitbutton = function(task) {
					var isValid = obj.valid();
					if(isValid){
						Joomla.submitform(task,obj[0]);
					} else {
						return false;
					}
				}

			});

		</script>
		<?php $doc->addCustomTag(ob_get_clean());

		static::$loaded[__METHOD__] = true;
	}
}
