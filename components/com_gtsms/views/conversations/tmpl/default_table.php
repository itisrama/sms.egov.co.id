<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
$isData	= count($this->items);
?>

<table id="messageTable" class="adminlist table table-striped table-bordered">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('COM_GTSMS_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="text-center" colspan="3">
				<?php echo GTHelperHTML::gridSort('COM_GTSMS_FIELD_CONTACT', 'a.contact', $this->ordering, $this->direction); ?>
			</th>
			<th class="text-center">
				<?php echo GTHelperHTML::gridSort('COM_GTSMS_FIELD_MESSAGE', 'a.message', $this->ordering, $this->direction); ?>
			</th>
			<th width="14%" class="text-center">
				<?php echo GTHelperHTML::gridSort('COM_GTSMS_FIELD_DATE', 'a.date', $this->ordering, $this->direction); ?>
			</th>
			<th style="width:50px" class="text-center">
				<?php echo JText::_('COM_GTSMS_ACTION'); ?>
			</th>
		</tr>
	</thead>
	<tbody class="rowData" style="<?php echo $isData ? '': 'display:none'?>">
		<?php array_unshift($this->items, ''); ?>
		<?php foreach ($this->items as $i => $item): ?>
			<tr style="<?php echo $i ? '': 'display:none'?>">
				<td class="text-center id">
					<?php echo @$item->id;?>
				</td>
				<td class="type">
					<?php echo @$item->type;?>
				</td>
				<td class="contact text-left">
					<?php echo @$item->contact;?> 
				</td>
				<td class="count text-right">
					<?php echo @$item->count;?>
				</td>
				<td class="message text-left">
					<?php echo @$item->message;?>
				</td>
				<td class="date text-center">
					<?php echo @$item->date;?>
				</td>
				<td class="button text-center">
					<?php echo @$item->button;?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tbody class="rowNull" style="<?php echo $isData ? 'display:none': ''?>">
		<tr>
			<td class="text-center" colspan="8">
				<?php echo JText::_('COM_GTSMS_NO_DATA'); ?>
			</td>
		</tr>
	</tbody>
</table>