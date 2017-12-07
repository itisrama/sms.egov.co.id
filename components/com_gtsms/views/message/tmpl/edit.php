<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$html	= array();

$html[]	= GTHelperFieldset::renderEdit($this->form->getFieldset('item'));

$html	= implode(PHP_EOL, $html);

?>
<div id="com_gtprojplan" class="item-page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->page_title; ?></h1>
	</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_gtsms&layout=edit'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal form-validation">
		<?php echo $html;?><br/>
		<?php echo GTHelperFieldset::tplEditField(null, '
			<button type="button" class="btn btn-blue btn-lg" onclick="Joomla.submitbutton(\'message.newsms\')">
				<i class="fa fa-envelope"></i> '.JText::_('COM_GTSMS_TOOLBAR_SEND_MESSAGE').'
			</button>
		');?>
		<input type="hidden" name="id" value="<?php echo isset($this->item->id) ? $this->item->id : 0 ?>" />
		<input type="hidden" name="cid[]" value="<?php echo isset($this->item->id) ? $this->item->id : 0 ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
