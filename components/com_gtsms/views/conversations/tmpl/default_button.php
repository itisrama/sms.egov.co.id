<?php if($this->canCreate || $this->canEditState || $this->canDelete):?>
<div class="command form-inline">
	<?php if($this->state->get('filter.published') == -2):?>
		<button type="button" class="btn btn-default" onclick="submitbuttonlist('conversations.publish')">
			<i class="fa fa-undo"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_RESTORE')?>
		</button>
	<?php endif;?>
	<button type="button" class="btn btn-default" onclick="jQuery('#table-filter').slideToggle();">
		<i class="fa fa-filter"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TOGGLE_FILTER')?>
	</button>
	<button type="button" class="btn btn-blue" onclick="submitbuttonlist('conversations.markRead')">
		<i class="fa fa-eye"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_MARK_READ')?>
	</button>
	<button type="button" class="btn btn-orange" onclick="submitbuttonlist('conversations.markUnread')">
		<i class="fa fa-eye-slash"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_MARK_UNREAD')?>
	</button>
	<div class="pull-right">
		<?php if($this->canDelete):?>
			<?php if($this->state->get('filter.published') == -2):?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('conversations.delete')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH_PERMANENTLY')?>
				</button>
			<?php else:?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('conversations.trash')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH')?>
				</button>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
<?php endif;?>
