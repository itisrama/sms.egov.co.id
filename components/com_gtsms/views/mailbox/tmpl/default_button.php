<?php if($this->canCreate || $this->canEditState || $this->canDelete):?>
<div class="command form-inline">
	<button type="button" class="btn btn-default" onclick="jQuery('#table-filter').slideToggle();">
		<i class="fa fa-filter"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TOGGLE_FILTER')?>
	</button>
	
	<?php if($this->state->get('filter.published') == -2):?>
		<button type="button" class="btn btn-default" onclick="submitbuttonlist('mailbox.publish')">
			<i class="fa fa-undo"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_RESTORE')?>
		</button>
	<?php endif;?>
	<?php echo GTHelperHtml::getDropdown('change_category_id', 'COM_GTSMS_TOOLBAR_CHANGE_CATEGORY', 'messages.changeCategory', $this->categories, 'primary'); ?>
	
	<div class="pull-right">
		<?php if($this->canDelete):?>
			<?php if($this->state->get('filter.published') == -2):?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('mailbox.delete')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH_PERMANENTLY')?>
				</button>
			<?php else:?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('mailbox.trash')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH')?>
				</button>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
<?php endif;?>
