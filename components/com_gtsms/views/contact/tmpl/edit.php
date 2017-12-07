<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
?>
<div id="com_gtprojplan" class="item-page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->page_title; ?></h1>
	</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_gtsms'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal form-validation">
		<?php echo $this->loadTemplate('button'); ?>
		<br/>

		<legend><?php echo JText::_('COM_GTSMS_GENERAL')?></legend>
		<?php echo GTHelperFieldset::renderEdit($this->form->getFieldset('item'));?>
		<?php if(GTHelperAccess::isAdmin()):?>
			<?php echo GTHelperFieldset::renderEdit($this->form->getFieldset('categories'));?>
		<?php endif;?>
		<br/>
		<legend><?php echo JText::_('COM_GTSMS_METADATA')?></legend>
		<?php echo GTHelperFieldset::renderEdit($this->form->getFieldset('meta'));?>

		<input type="hidden" name="id" value="<?php echo isset($this->item->id) ? $this->item->id : 0 ?>" />
		<input type="hidden" name="cid[]" value="<?php echo isset($this->item->id) ? $this->item->id : 0 ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
