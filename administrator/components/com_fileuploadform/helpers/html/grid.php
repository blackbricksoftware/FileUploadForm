<?php defined('JPATH_PLATFORM') or die;

abstract class FUFHtmlGrid {

	protected static $loaded = array();

	/* Mimics JHtmlGrid::sort, but accounts for not always using adminForm */
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '', $form='jQuery(this).closest(\'form\')[0]') {

		JHtml::_('fufhtml.jquery.framework');
		JHtml::_('behavior.tooltip');

		$direction = strtolower($direction);
		$icon = array('arrow-up-3', 'arrow-down-3');
		$index = (int) ($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="#" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\','.$form.');return false;"'
			. ' class="hasTip" title="' . JText::_($tip ? $tip : $title) . '::' . JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
		$html .= JText::_($title);

		if ($order == $selected)
		{
			$html .= ' <i class="icon-' . $icon[$index] . '"></i>';
		}

		$html .= '</a>';

		return $html;
	}
}
