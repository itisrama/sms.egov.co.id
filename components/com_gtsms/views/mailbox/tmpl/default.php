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
		<h1><?php echo $this->page_title; ?></h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_gtsms'); ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->ordering; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->direction; ?>" />
		<?php echo JHtml::_('form.token'); ?>

		<?php if(!$this->user->guest):?>
			<?php echo $this->loadTemplate('button'); ?>
		<?php endif;?>

		<div id="table-filter">
			<?php echo $this->loadTemplate('form'); ?><br/>
		</div>
		
		<div class="table-responsive">
			<?php echo $this->loadTemplate('table'); ?>
		</div>

		<div class="text-center">
			<div style="display:inline-block">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
	</form>
</div>
