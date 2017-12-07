<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div id="mod_gtsms_status-<?php echo $module->id?>" class="mod_gtsms_status row-fluid">
	<h3><?php echo JText::_('MOD_GTSMS_STATUS_MODEM_STATUS')?></h3>
	<?php foreach($modems as $modem):?>
	<div class="col-xs-6 modem-container"><div class="modem <?php echo $modem->name?>" data-toggle="modal" data-target="#modem-<?php echo $modem->name?>">
		<?php echo $modem->name?><span style="display:none" class="fa fa-refresh fa-spin"></span>
	</div></div>
	<?php endforeach;?>
</div>

<?php foreach($modems as $modem):?>
<div id="modem-<?php echo $modem->name?>" class="modem-<?php echo $modem->name?> modem modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title"><?php echo $modem->msisdn ? '<strong>'.$modem->name.'</strong> - '.GTHelperNumber::setMSISDN($modem->msisdn) : '<strong>'.$modem->name.'</strong>' ?></h3>
			</div>
			<div class="modal-body row-fluid">
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_CARRIER')?></label>
					<div class="col-sm-8"><?php echo $modem->carrier ? $modem->carrier : '-' ?></div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_DESCRIPTION')?></label>
					<div class="col-sm-8"><?php echo $modem->description ? $modem->description : '-' ?></div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_STRENGTH')?></label>
					<div class="col-sm-8 strength">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_QUALITY')?></label>
					<div class="col-sm-8 quality">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_ACTIVITY')?></label>
					<div class="col-sm-8 activity">-</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-sm-4 text-right"><?php echo JText::_('MOD_GTSMS_STATUS_FIELD_UPDATE')?></label>
					<div class="col-sm-8 datetime">-</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php endforeach;?>