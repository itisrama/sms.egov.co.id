<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
$isNew = $count->new;
?>
<div class="mod_gtsms_count">
	<input type="hidden" id="countMsg" value="<?php echo $count->new?>" />
	<input type="hidden" id="countSent" value="<?php echo $count->sent?>" />
	<input type="hidden" id="countFailed" value="<?php echo $count->failed?>" />
	<div id="countMessages"<?php echo $isNew ? ' style="display:none"' : ''; ?>>
		<?php echo JText::_('MOD_GTSMS_COUNT_HEAD');?> : &nbsp;
		<span title="<?php echo JText::_('MOD_GTSMS_COUNT_RECEIVED')?>"><i class="fa fa-arrow-down"></i> <span class="received"><?php echo $count->received?></span></span>&nbsp;|&nbsp;
		<span title="<?php echo JText::_('MOD_GTSMS_COUNT_SENT')?>"><i class="fa fa-arrow-up"></i> <span class="sent"><?php echo $count->sent?></span></span>
	</div>
	<a id="alertMessages"<?php echo $isNew ? '' : ' style="display:none"'; ?> href="<?php echo $inbox_url;?>">
		<i class="fa fa-info-circle"></i>&nbsp;
		<span class="msg"><?php echo sprintf(JText::_('MOD_GTSMS_COUNT_N_NEW_MESSAGES'), $count->new)?></span></span>
	</a>
</div>