<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$html	= array();	

$html[]	= '<hr>';
$html[]	= GTHelperFieldset::renderView($this->form->getFieldset('item'));

$html	= implode(PHP_EOL, $html);

?>
<div id="com_gtprojplan" class="item-page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->page_title; ?></h1>
	</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_gtsms'); ?>" method="post" id="adminForm">
		<?php echo $this->loadTemplate('button'); ?>
		<input type="hidden" name="id" value="<?php echo @$this->item->id ?>" />
		<input type="hidden" name="cid[]" value="<?php echo @$this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
	<br/>

	<h3><?php echo JText::_('COM_GTSMS_GENERAL')?></h3>
	<?php echo GTHelperFieldset::renderView($this->form->getFieldset('item'));?>
	
	<h3><?php echo JText::_('COM_GTSMS_METADATA')?></h3>
	<?php echo GTHelperFieldset::renderView($this->form->getFieldset('meta'));?>
</div>
