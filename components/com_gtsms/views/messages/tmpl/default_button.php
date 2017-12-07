<div class="command form-inline clearfix">
	<button type="button" class="btn btn-default" onclick="jQuery('#table-filter').slideToggle();">
		<i class="fa fa-filter"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TOGGLE_FILTER')?>
	</button>
	
	<?php if($this->state->get('filter.published') == -2):?>
		<button type="button" class="btn btn-default" onclick="submitbuttonlist('messages.publish')">
			<i class="fa fa-undo"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_RESTORE')?>
		</button>
	<?php endif;?>
	<a class="btn btn-blue" href="#reply">
		<i class="fa fa-mail-reply"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_REPLY_MESSAGE')?>
	</a>
	<?php echo GTHelperHtml::getDropdown('change_category_id', 'COM_GTSMS_TOOLBAR_CHANGE_CATEGORY', 'messages.changeCategory', $this->categories, 'primary'); ?>

	<div class="pull-right">
		<button type="button" class="btn btn-orange" onclick="Joomla.submitbutton('messages.back')">
			<i class="fa fa-arrow-left"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_BACK')?>
		</button>

		<?php if($this->canDelete):?>
			<?php if($this->state->get('filter.published') == -2):?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('messages.delete')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH_PERMANENTLY')?>
				</button>
			<?php else:?>
				<button type="button" class="btn btn-red" onclick="submitbuttonlist('messages.trash')">
					<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_TRASH')?>
				</button>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
