<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

$donuts		= array();
foreach ($this->items as $type => $cats) {
	foreach ($cats as $cat_id => $count) {
		$donuts[$type][] = array(
			'value' => $count, 
			'label' => $this->categories[$cat_id]
		);
	}
}
$total = array();
$types = array(
	'received'	=> JText::_('COM_GTSMS_TYPE_RECEIVED'),
	'process'	=> JText::_('COM_GTSMS_TYPE_PROCESS'),
	'sent'		=> JText::_('COM_GTSMS_TYPE_SENT'),
	'failed'	=> JText::_('COM_GTSMS_TYPE_FAILED')
);
$iconTypes = array(
	'received'	=> 'arrow-down',
	'sent'		=> 'check',
	'process'	=> 'arrow-up',
	'failed'	=> 'ban'
);

GTHelperMorris::load();
?>

<div class="row-fluid">
	<?php foreach ($donuts as $type => $donut):?>
		<?php if($type == 'process') continue;?>
		<div class="col-md-4">
			<h2 class="text-center"><span class="fa fa-<?php echo $iconTypes[$type]?>"></span> <?php echo JText::_('COM_GTSMS_OPT_TYPE_'.strtoupper($type));?></h2>
			<?php echo GTHelperMorris::donut($donut, "x") ?>
		</div>
	<?php endforeach;?>
</div>

<div class="table-responsive"><table class="adminlist table table-striped table-bordered">
	<thead>
		<tr>
			<th class="text-center" style="width:20px"><?php echo JText::_('COM_GTSMS_NUM')?></th>
			<th><?php echo JText::_('COM_GTSMS_FIELD_CATEGORY_ID')?></th>
			<?php foreach ($types as $type => $type_label):?>
				<?php $total[$type] = 0;?>
				<th class="text-center"><span class="fa fa-<?php echo $iconTypes[$type]?>"></span> <?php echo $type_label?></th>
			<?php endforeach;?>
		</tr>
	</thead>
	<tbody>
		<?php $i=1;?>
		<?php foreach ($this->categories as $cat_id => $cat):?>
			<tr>
				<td class="text-center"><?php echo $i; $i++;?></td>
				<td><?php echo $cat?></td>
				<?php foreach (array_keys($types) as $type):?>
					<?php $count		= intval(@$this->items[$type][$cat_id]);?>
					<?php $total[$type]	+= $count;?>
					<td class="text-center"><?php echo $count ? $count : '-'?></td>
				<?php endforeach;?>
			</tr>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td class="text-center"><strong>&Sigma;</strong></td>
			<td><strong>
				<?php echo JText::_('COM_GTSMS_TOTAL_MESSAGE')?>
			</strong></td>
			<?php foreach (array_keys($types) as $type):?>
				<td class="text-center"><strong>
					<?php echo $total[$type] ? $total[$type] : '-'?>
				</strong></td>
			<?php endforeach;?>
		</tr>
	</tfoot>
</table></div>
