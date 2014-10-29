<?php defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.view');

class FUFBackView extends JViewLegacy {

	function __construct() {

		$this->view = $this->getName();
		$this->layout = $this->getLayout();
		$this->link = 'index.php?option=com_fileuploadform&view='.$this->view.($this->layout!='default'?'&layout='.$this->layout:'');

		parent::__construct();
	}

	public function display($tpl=null) {

		$user = JFactory::getUser();

		JToolbarHelper::title(JString::ucwords(implode(' ',(JStringNormalise::fromCamelCase($this->view,true)))));

		if ($user->authorise('core.admin','com_fileuploadform')) JToolbarHelper::preferences('com_fileuploadform');

		parent::display($tpl);
	}

	protected function addNewButton($text='<i class="icon-new icon-white"></i> New', $target='#newItem', $class='btn btn-small btn-primary') {
		JHtml::_('fufhtml.backview.newitem',$target);
		$this->addCollapseButton($text,$target,$class);
	}
	protected function addCollapseButton($text, $target, $class = 'btn btn-small') {
		JHtml::_('fufhtml.bootstrap.framework');
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Custom','
			<button class="'.$this->escape($class).'" type="button" data-toggle="collapse" data-target="'.$this->escape($target).'">
				'.$text.'
			</button>
		');
	}

	protected function addPublishButtons($delete=true) {

		$publishOptions = $this->getPublishOptions();
		$filter_state = $this->state->get('filter.state');

		if (
			( !array_key_exists('published',$publishOptions) || $publishOptions['published'] ) &&
			$filter_state!=1
		) {
			JToolbarHelper::publishList($this->view.'.publish');
		}
		if (
			( !array_key_exists('unpublish',$publishOptions) || $publishOptions['unpublish'] ) &&
			$filter_state!='0'
		) {
			JToolbarHelper::unpublishList($this->view.'.unpublish');
		}
		if (
			( !array_key_exists('archived',$publishOptions) || $publishOptions['archived'] ) &&
			$filter_state!=2
		) {
			JToolbarHelper::archiveList($this->view.'.archive');
		}
		if (
			( !array_key_exists('trash',$publishOptions) || $publishOptions['trash'] ) &&
			$filter_state!=-2
		) {
			JToolbarHelper::trash($this->view.'.trash');
		} elseif ($delete) {
			 JToolbarHelper::deleteList('Are you sure?',$this->view.'.delete');
		}
	}

	protected function getSortFields() {
		return array();
	}

	protected function getPublishOptions() {
		return array(
			'published'		=> true,
			'unpublished'	=> true,
			'archived'		=> true,
			'trash'			=> true,
			'all'			=> true,
		);
	}

	protected function sidebarRender() { ob_start(); ?>

		<ul class="nav nav-list">
<?php /*
			<li class="nav-header">Assign</li>
*/ ?>
			<li class="<?= $this->view=='uploads'?'active':'' ?>"><a href="<?= JRoute::_('index.php?option=com_fileuploadform&view=uploads') ?>">Uploads</a></li>
		</ul>
		<hr class="hr-condensed" />

	<?php return ob_get_clean(); }

	protected function filterRender() { ob_start(); ?>

		<select name="filter_state" id="filter_state" class="input-medium" onchange="this.form.submit()">
			<?= JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',$this->getPublishOptions()), 'value', 'text', $this->state->get('filter.state'),true) ?>
		</select>

		<script type="text/javascript">
			jQuery(function($){
				// regular selects
				$('select#filter_state').select2();
			});
		</script>

		<hr class="hr-condensed" />
	<?php return ob_get_clean(); }

	protected function headerRender($checkbox=true,$search=true,$total=true) { ob_start();

		JHtml::_('behavior.framework');
		JHtml::_('fufhtml.jquery.select2');

		$sortFields = $this->get('FilterFields');

		$listOrder = $this->state->get('list.ordering');
		$listDirn = $this->state->get('list.direction');

		?>

		<script type="text/javascript">
			jQuery(function($){
				// regular selects
				$('#filter-bar select').select2();
			});
			Joomla.orderTable = function()
			{
				table = document.getElementById("sortTable");
				direction = document.getElementById("directionTable");
				order = table.options[table.selectedIndex].value;
				if (order != '<?php echo $listOrder; ?>')
				{
					dirn = 'asc';
				}
				else
				{
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, '');
			}
		</script>

		<div id="filter-bar">

			<?php if ($checkbox) { ?>
				<div class="pull-left" style="padding: 0 10px;"><?= JHtml::_('grid.checkall') ?></div>
			<?php } ?>

			<?php if ($search) { ?>
				<div class="filter-search pull-left" style="margin-right: 5px;">
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_LATENGIHT_FILTER_SEARCH_DESC'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				</div>
			<?php } ?>

			<?php if ($total) { ?>
				<div class="pull-left" style="padding: 5px 10px; font-weight: bold;">Total: <?= $this->get('Total') ?></div>
			<?php } ?>

			<?php if (isset($this->pagination)) { ?>
				<div class="pull-right" style="margin-left: 5px;"><?= $this->pagination->getLimitBox() ?></div>
			<?php } ?>

			<?php if (!empty($sortFields)) { ?>
				<div class="pull-right" style="margin-left: 5px;">
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
					</select>
				</div>
				<div class="pull-right" style="margin-left: 5px;">
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?= JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder) ?>
					</select>
				</div>
			<?php } ?>

		</div>
		<div class="clearfix"></div>

	<?php return ob_get_clean(); }
}
