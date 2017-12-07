<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<div id="com_gtsms" class="item-page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading', 1)): ?>
	<div class="page-header">
		<h1><?php echo JText::_('COM_GTSMS_FIELD_CONVERSATION').' : ' .$this->contact ?></h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_gtsms&view=messages&layout=view&id='.$this->input->get('id')) ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->ordering; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->direction; ?>" />
		<?php echo JHtml::_('form.token'); ?>

		<?php if(!$this->user->guest):?>
			<?php echo $this->loadTemplate('button'); ?>
		<?php endif;?>

		<?php echo $this->loadTemplate('form'); ?>
		<br/>
		<div class="table-responsive">
			<?php echo $this->loadTemplate('table'); ?>
		</div>

		<div class="text-center">
			<div style="display:inline-block">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
		<?php if($this->msisdn->type == 'numeric'):?>
			<hr/>
			<?php echo $this->loadTemplate('newsms'); ?>
		<?php endif;?>
	</form>
</div>
