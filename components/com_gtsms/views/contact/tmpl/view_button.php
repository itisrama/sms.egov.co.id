<div class="command form-inline">
	<?php if($this->canEdit):?>
		<button type="button" class="btn btn-blue" onclick="Joomla.submitbutton('contact.edit')">
			<i class="fa fa-edit"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_EDIT')?>
		</button>
	<?php endif;?>

	<div class="pull-right">
		<button type="button" class="btn btn-orange" onclick="Joomla.submitbutton('contact.back')">
			<i class="fa fa-arrow-left"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_BACK')?>
		</button>
		<?php if($this->isTrashed  && $this->canDelete):?>
			<button type="button" class="btn btn-red" onclick="Joomla.submitbutton('contacts.deleteList')">
				<i class="fa fa-trash"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH_PERMANENTLY')?>
			</button>
		<?php elseif($this->canEditState):?>
			<button type="button" class="btn btn-red" onclick="Joomla.submitbutton('contacts.trash')">
				<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH')?>
			</button>
		<?php endif;?>
	</div>
</div>
