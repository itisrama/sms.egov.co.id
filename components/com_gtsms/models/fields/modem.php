<?php
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldModem extends JFormFieldList
{
	protected $type = 'Modem';
	
	protected function getOptions() {
		// Get Feeder ID
		$input		= JFactory::getApplication()->input;

		// DB Objects
		$db	= JFactory::getDBO();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.name', 'a.description')));
		$query->from($db->quoteName('#__gtsms_modems', 'a'));

		// Join contact
		$query->select('CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`) msisdn');
		$query->join('LEFT', $db->quoteName('#__gtsms_msisdns', 'b') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.id'));

		$query->where($db->quoteName('a.published').' = 1');

		if(!GTHelperAccess::isAdmin()) {
			$user = JFactory::getUser();
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'd') . ' ON FIND_IN_SET('.$db->quoteName('d.id').', '.$db->quoteName('a.category_ids') . ')');
			$query->where('FIND_IN_SET('.$user->id.', '.$db->quoteName('d.user_ids').')');
		}

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		$modems = $db->loadObjectList();
		$options	= array();
		foreach ($modems as &$modem) {
			$msisdn = $modem->name.' (';
			$msisdn .= $modem->description ? $modem->description.' - ' : '';
			$msisdn .= GTHelperNumber::setMSISDN($modem->msisdn).')';

			$options[] = JHtml::_('select.option', $modem->name, $msisdn);
		}
		
		// Merge any additional options in the XML definition.
		$options	= array_merge(parent::getOptions(), $options);

		return $options;
	}
}
?>
