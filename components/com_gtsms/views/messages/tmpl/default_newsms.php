<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div id="reply" class="form-horizontal">
	<h2><?php echo sprintf(JText::_('COM_GTSMS_PT_REPLY'), $this->contact)?></h2><br/>
	<input type="hidden" name="msisdn" value="<?php echo $this->msisdn->msisdn_raw?>"/>
	
	<div class="form-group">
		<label for="modem" class="col-sm-2 control-label"><?php echo JText::_('COM_GTSMS_FIELD_MODEM')?></label>
		<div class="col-sm-10">
			<select name="modem" class="input-large">
				<?php echo JHtml::_('select.options', $this->modems, 'name', 'msisdn', @$item->modem ? $item->modem : $this->state->get('filter.modem'), true);?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="category" class="col-sm-2 control-label"><?php echo JText::_('COM_GTSMS_FIELD_CATEGORY_ID')?></label>
		<div class="col-sm-10">
			<select name="category_id" class="input-large">
				<option value="0"><?php echo JText::_('COM_GTSMS_OPT_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', $this->categories, 'id', 'name', @$item->category_id ? $item->category_id : $this->state->get('filter.category'), true);?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="message" class="col-sm-2 control-label"><?php echo JText::_('COM_GTSMS_FIELD_MESSAGE')?></label>
		<div class="col-sm-10">
			<textarea id="message" name="message" class="input-xxlarge" rows="4"></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-2"></div>
		<div class="col-sm-10">
			<button type="button" class="btn btn-blue" onclick="Joomla.submitbutton('message.send')">
				<i class="fa fa-envelope"></i> <?php echo JText::_('COM_GTSMS_TOOLBAR_SEND_MESSAGE')?>
			</button>
		</div>
	</div>
</div>