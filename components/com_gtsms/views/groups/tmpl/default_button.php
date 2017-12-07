<?php if($this->canCreate || $this->canEditState || $this->canDelete):?>
<div class="command form-inline">
	<?php if($this->canCreate):?>
		<button type="button" class="btn btn-success" onclick="Joomla.submitbutton('group.add')">
			<i class="fa fa-plus-circle"></i> <?php echo str_replace('%s', JText::_('COM_GTSMS_GROUP'), JText::_('COM_GTSMS_PT_NEW'))?>
		</button>
	<?php endif;?>

	<button type="button" class="btn btn-default" onclick="jQuery('#table-filter').slideToggle();">
		<i class="fa fa-filter"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TOGGLE_FILTER')?>
	</button>
	
	<?php if($this->state->get('filter.published') == -2):?>
		<button type="button" class="btn btn-default" onclick="submitbuttonlist('groups.publish')">
			<i class="fa fa-undo"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_RESTORE')?>
		</button>
	<?php endif;?>

	<div class="pull-right">
		<?php if($this->canDelete):?>
			<?php if($this->state->get('filter.published') == -2):?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('groups.delete')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH_PERMANENTLY')?>
				</button>
			<?php else:?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('groups.trash')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH')?>
				</button>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
<?php endif;?>
