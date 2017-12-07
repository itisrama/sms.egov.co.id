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
		
		<div class="table-responsive"><table class="adminlist table table-striped table-bordered">
			<thead>
				<tr>
					<th style="width:1%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('COM_GTSMS_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th style="width:5%" class="text-center">
						<?php echo GTHelperHTML::gridSort('JGRID_HEADING_ID', 'a.id', $this->ordering, $this->direction); ?>
					</th>
					<th class="text-center">
						<?php echo GTHelperHTML::gridSort('COM_GTSMS_FIELD_NAME', 'a.name', $this->ordering, $this->direction); ?>
					</th>
					<th style="width:15%" class="text-center">
						<?php echo JText::_('COM_GTSMS_FIELD_MSISDN'); ?>
					</th>
					<th width="15%" class="text-center">
						<?php echo GTHelperHTML::gridSort('COM_GTSMS_FIELD_CREATED_MODIFIED_DATE', 'a.date', $this->ordering, $this->direction); ?>
					</th>
					<th style="width:83px" class="text-center">
						<?php echo JText::_('COM_GTSMS_ACTION'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!$this->items): ?>
					<tr class="row0">
						<td class="text-center" colspan="8">
							<?php echo JText::_('COM_GTSMS_NO_DATA'); ?>
						</td>
					</tr>
				<?php else: foreach ($this->items as $i => $item): ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="text-center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="text-center">
							<?php echo $item->id; ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_($this->viewUrl . (int)$item->id) ?>">
								<?php echo $item->name;?>
							</a>
						</td>
						<td class="text-center">
							<?php echo $item->msisdns; ?>
						</td>
						<td class="text-center">
							<?php echo $item->date;?><br/>
							<small><?php echo $item->diff;?></small>
						</td>
						<td class="text-center">
							<div class="btn-group">
								<a title="<?php echo JText::_('COM_GTSMS_TOOLBAR_VIEW') ?>" href="<?php echo JRoute::_($this->viewUrl . (int)$item->id) ?>" class="btn btn-default btn-sm hasTooltip"><i class="fa fa-file-text"></i></a>
								<a title="<?php echo JText::_('COM_GTSMS_TOOLBAR_EDIT') ?>" href="<?php echo JRoute::_($this->editUrl . (int)$item->id); ?>" class="btn btn-default btn-sm hasTooltip"><i class="fa fa-edit"></i></a>
							</div>
						</td>
					</tr>
				<?php endforeach; endif; ?>
			</tbody>
		</table></div>

		<div class="text-center">
			<div style="display:inline-block">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
	</form>
</div>
