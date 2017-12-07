<?php 
	$publishedOptions = JHtml::_('jgrid.publishedOptions');
	$publishedOptions = array_slice($publishedOptions, 3);

	$searchBy = array(
		'contact' => JText::_('COM_GTSMS_FIELD_CONTACT'), 
		'message' => JText::_('COM_GTSMS_FIELD_MESSAGE')
	);

	$types = array(
		'new' => JText::_('COM_GTSMS_OPT_TYPE_NEW'), 
		'unread' => JText::_('COM_GTSMS_OPT_TYPE_UNREAD'), 
		'read' => JText::_('COM_GTSMS_OPT_TYPE_READ'), 
		'received' => JText::_('COM_GTSMS_OPT_TYPE_RECEIVED'), 
		'process' => JText::_('COM_GTSMS_OPT_TYPE_PROCESS'), 
		'sent' => JText::_('COM_GTSMS_OPT_TYPE_SENT'), 
		'failed' => JText::_('COM_GTSMS_OPT_TYPE_FAILED')
	);
?>
<div class="form-inline">
	<div class="form-group">
		<div class="input-group input-xlarge">
			<input <?php echo $this->state->get('filter.published') == '-2' ? 'disabled' : null ?> class="form-control" name="filter_search" type="text" value="<?php echo $this->escape($this->state->get('filter.search')); ?>"  placeholder="<?php echo JText::_('COM_GTSMS_FIND_DESC') ?>" id="filter_search">
			<div class="input-group-btn">
				<a class="btn btn-default" onclick="document.getElementById('filter_search').value='';document.getElementById('adminForm').submit();">
					<i class="fa fa-times"></i>
				</a>
				<?php echo GTHelperHtml::getDropdown('filter_search_by', 'COM_GTSMS_FIND', null, $searchBy, 'info', false, false, $this->state->get('filter.search_by')); ?>
			</div>
		</div>
	</div>
	<div class="pull-right">
		<div class="form-group">
			<label for="limit"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="form-group">
			<select name="filter_published" class="inputbox" onchange="document.getElementById('adminForm').submit()" style="width:auto">
				<option value="null"><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', $publishedOptions, 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
		</div>
	</div>
</div>
<hr/>
<div class="form-inline">
	<div class="form-group" style="margin-right:10px">
		<label for="filter_type"><?php echo JText::_('COM_GTSMS_FIELD_MESSAGE_TYPE'); ?>&nbsp;</label>
		<select name="filter_type" class="inputbox" onchange="document.getElementById('adminForm').submit()" style="width:auto">
			<option value="0"><?php echo JText::_('COM_GTSMS_OPT_SELECT_TYPE');?></option>
			<?php echo JHtml::_('select.options', $types, 'id', 'name', $this->state->get('filter.type'), true);?>
		</select>
	</div>
	<div class="form-group">
		<label for="filter_modem"><?php echo JText::_('COM_GTSMS_FIELD_MODEM'); ?>&nbsp;</label>
		<select name="filter_modem" class="inputbox" onchange="document.getElementById('adminForm').submit()" style="width:auto">
			<option value="0"><?php echo JText::_('COM_GTSMS_OPT_SELECT_MODEM');?></option>
			<?php echo JHtml::_('select.options', $this->modems, 'name', 'name', $this->state->get('filter.modem'), true);?>
		</select>
	</div>
	
	<div class="pull-right">
		<div class="form-group">
			<label for="filter_category"><?php echo JText::_('COM_GTSMS_FIELD_CATEGORY_ID'); ?>&nbsp;</label>
			<select name="filter_category" class="inputbox" onchange="document.getElementById('adminForm').submit()" style="width:auto">
				<option value="0"><?php echo JText::_('COM_GTSMS_OPT_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', $this->categories, 'id', 'name', $this->state->get('filter.category'), true);?>
			</select>
		</div>
	</div>
</div>
