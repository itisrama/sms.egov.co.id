<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="row-fluid">
<?php foreach($this->modems as $modem):?>
	<div class="col-sm-6">
		<div class="modem-container">
			<h2 class="modem <?php echo $modem->name?>">
				<?php echo $modem->name?><span style="display:none" class="fa fa-refresh fa-spin"></span>
			</h2>
			<div class="modem-<?php echo $modem->name?> row-fluid">
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_CARRIER')?></label>
					<div class="col-sm-8"><?php echo $modem->carrier ? $modem->carrier : '-' ?></div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_DESCRIPTION')?></label>
					<div class="col-sm-8"><?php echo $modem->description ? $modem->description : '-' ?></div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_STRENGTH')?></label>
					<div class="col-sm-8 strength">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_QUALITY')?></label>
					<div class="col-sm-8 quality">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_ACTIVITY')?></label>
					<div class="col-sm-8 activity">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('COM_GTSMS_FIELD_UPDATE')?></label>
					<div class="col-sm-8 datetime">-</div>
				</div>
			</div>
		</div>
	</div>
<?php endforeach;?>
</div>