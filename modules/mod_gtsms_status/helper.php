<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

// loads module function file
jimport('joomla.event.dispatcher');

class modGTSMSStatus {

	public static function getModems() {
		// Get a db connection.
		$db = JFactory::getDBO();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select Prices
		$query->select($db->quoteName(array('a.name', 'a.description')));
		$query->from($db->quoteName('#__gtsms_modems', 'a'));

		// Join Price Details
		$query->select('CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`) msisdn');
		$query->select($db->quoteName(array('b.carrier')));
		$query->join('LEFT', $db->quoteName('#__gtsms_msisdns', 'b') . 
			' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.id'));
		
		// WHERE
		$query->where($db->quoteName('a.published') . ' = 1');

		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}