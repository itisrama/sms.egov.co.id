<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;


class GTSMSModelDashboard extends GTModelList {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.created', $direction = 'desc') {
		parent::populateState($ordering, $direction);

		// Adjust the context to support modal layouts.
		$layout = $this->input->get('layout', 'default');
		if ($layout) {
			$this->context.= '.' . $layout;
		}
		
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$modem = $this->getUserStateFromRequest($this->context . '.filter.modem', 'filter_modem', '');
		$this->setState('filter.modem', $modem);

		$this->setState('list.limit', 0);
		$this->setState('list.start', 0);
	}

	protected function getListQuery() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);


		$query->select('COUNT(DISTINCT '.$db->quoteName('a.id').') count');
		$query->select($db->quoteName(array('a.category_id', 'a.type')));
		$query->from($db->quoteName('#__gtsms_messages', 'a'));
		
		$query->where($db->quoteName('a.msisdn_id').' > 0');
		$query->where($db->quoteName('a.modem').' IS NOT NULL');
		$query->where('('.$db->quoteName('a.modem').') <> ""');
		$query->where($db->quoteName('a.message').' IS NOT NULL');
		$query->where('('.$db->quoteName('a.message').') <> ""');

		$query->group($db->quoteName('a.category_id'));
		$query->group($db->quoteName('a.type'));

		//echo nl2br(str_replace('#__','eburo_',$query));

		return $query;
	}

	public function getItems($is_table = false) {
		$items = parent::getItems($is_table);
		$counts = array();

		foreach ($items as $item) {
			$counts[$item->type][$item->category_id] = $item->count;
		}

		return $counts;
	}

	public function getCategories() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));

		$query->where($db->quoteName('a.published').' = 1');

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		
		$categories = array();
		$categories[0]	= JText::_('COM_GTSMS_UNCATEGORIZED');

		foreach ($db->loadObjectList('id') as $k => $item) {
			$categories[$k] = $item->name;
		}

		return $categories;
	}

	public function getModems() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.name', 'a.description')));
		$query->from($db->quoteName('#__gtsms_modems', 'a'));

		// Join MSISDN
		$query->select('CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`) msisdn');
		$query->select($db->quoteName(array('b.carrier')));
		$query->join('LEFT', $db->quoteName('#__gtsms_msisdns', 'b') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.id'));

		$query->where($db->quoteName('a.published').' = 1');

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		$modems = $db->loadObjectList('name');
		foreach ($modems as &$modem) {
			$msisdn = $modem->msisdn ? GTHelperNumber::setMSISDN($modem->msisdn) : '';
			$msisdn = implode(' - ', array_filter(array($msisdn, $modem->carrier)));
			$msisdn = $msisdn ? $modem->name.' ('.$msisdn.')' : $modem->name;

			$modem->msisdn = $msisdn;
		}
		return $modems;
	}
}
