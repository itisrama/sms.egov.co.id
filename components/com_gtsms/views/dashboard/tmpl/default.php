<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
?>

<div id="com_gtsms" class="item-page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading', 1)): ?>
	<div class="page-header">
		<h1><?php echo $this->page_title; ?></h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_gtsms'); ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</form>

	
	<?php echo JHtml::_('bootstrap.startTabSet', 'recapDashboard', array('active' => 'count')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'recapDashboard', 'count', '<p class="lead"><i class="fa fa-pie-chart"></i> '.JText::_('COM_GTSMS_PT_MESSAGE_STAT', true).'</p>'); ?>
		<?php echo $this->loadTemplate('count'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'recapDashboard', 'modem', '<p class="lead"><i class="fa fa-signal"></i> '.JText::_('COM_GTSMS_PT_MODEM_STATUS', true).'</p>'); ?>
		<?php echo $this->loadTemplate('modem'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
</div>
