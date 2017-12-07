<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;


class GTSMSModelJson extends GTModel
{

	public function __construct($config = array()) {
		$config['load_params'] = false;
		parent::__construct($config);
	}

	public function countMessages($type = 'new') {
		$app		= JFactory::getApplication();
		$user 		= JFactory::getUser();

		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		if($type == 'unread') {
			$query->select('COUNT(DISTINCT '.$db->quoteName('a.msisdn_id').') count');
		} else {
			$query->select('COUNT(DISTINCT '.$db->quoteName('a.id').') count');
		}

		$query->from($db->quoteName('#__gtsms_messages', 'a'));
		

		if(!GTHelperAccess::isAdmin($user->id)) {
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'b') . ' ON ' . $db->quoteName('a.category_id') . ' = ' . $db->quoteName('b.id'));
			$query->where('FIND_IN_SET('.$user->id.', '.$db->quoteName('b.user_ids').')');
		}

		switch($type) {
			default:
				$query->where($db->quoteName('a.type'). ' = ' . $db->quote($type));
				break;
			case 'unread':
				// Join Read
				$query->join('LEFT', $db->quoteName('#__gtsms_reads', 'c') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('c.msisdn_id') .
					' AND ' . $db->quoteName('c.user_id') . ' = ' . $db->quote($user->id)
				);
				$query->where('('.$db->quoteName('c.type').' = '.$db->quote('unread').' OR '.$db->quoteName('c.type').' IS NULL)');
				$query->where($db->quoteName('a.type'). ' = ' . $db->quote('received'));
				break;
			case 'new':
				// Join Read
				$query->join('LEFT', $db->quoteName('#__gtsms_reads', 'c') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('c.msisdn_id') .
					' AND ' . $db->quoteName('c.user_id') . ' = ' . $db->quote($user->id)
				);
				$query->where('('.
					$db->quoteName('c.type').' IS NULL OR '.$db->quoteName('a.created') . ' >= ' . $db->quoteName('c.modified')
				.')');
				$query->where($db->quoteName('a.type'). ' = ' . $db->quote('received'));
				break;
		}
		
		$query->where($db->quoteName('a.published').' = 1');
		$query->where($db->quoteName('a.msisdn_id').' > 0');
		$query->where($db->quoteName('a.modem').' IS NOT NULL');
		$query->where('('.$db->quoteName('a.modem').') <> ""');
		$query->where($db->quoteName('a.message').' IS NOT NULL');
		$query->where('('.$db->quoteName('a.message').') <> ""');

		$db->setQuery($query);
		$result = $db->loadObject();

		//echo nl2br(str_replace('#__','eburo_',$query));

		return intval(@$result->count);
	}
}
