<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSModelContacts extends GTModelList
{
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('a.id', 'a.name', 'a.date');
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'a.id', $direction = 'desc') {
		parent::populateState($ordering, $direction);

		// Adjust the context to support modal layouts.
		$layout = $this->input->get('layout', 'default');
		if ($layout) {
			$this->context.= '.' . $layout;
		}
		
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '1');
		$this->setState('filter.published', $published);
	}
	
	protected function getListQuery() {
		
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Select item
		$query->select($db->quoteName(array('a.id', 'a.name', 'a.description', 'a.created')));
		$query->select('IF(DAY('.$db->quoteName('a.modified').'), '.$db->quoteName('a.modified').', '.$db->quoteName('a.created').') date');
		$query->from($db->quoteName('#__gtsms_contacts', 'a'));

		// Join msisdn
		$query->select('GROUP_CONCAT(CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`)) msisdns');
		$query->join('INNER', $db->quoteName('#__gtsms_msisdns', 'b') . ' ON FIND_IN_SET(' . $db->quoteName('b.id') . ', ' . $db->quoteName('a.msisdn_ids') . ')');

		if(!GTHelperAccess::isAdmin()) {
			$categories	= array_map(array($db, 'quote'), array_keys($this->getCategories()));
			$categories	= $categories ? implode(',', $categories) : $db->quote('-99');

			$query->where($db->quoteName('a.category_ids'). ' IN ('.$categories.')');
		}
		
		// Publish filter
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int)$published);
		} else {
			$query->where('a.published IN (0, 1)');
		}
		
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			
			// If contains spaces, the words will be used as keywords.
			if (preg_match('/\s/', $search)) {
				$search = str_replace(' ', '%', $search);
			}
			$search = $db->quote('%' . $search . '%');
			
			$search_query = array();
			$search_query[] = $db->quoteName('a.name') . 'LIKE ' . $search;
			$search_query[]	= 'CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`) LIKE ' . $search;
			$query->where('(' . implode(' OR ', $search_query) . ')');
		}
		
		$query->group($db->quoteName('a.id'));

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		switch ($orderCol) {
			case 'a.date' :
				$query->order('IF(DAY('.$db->quoteName('a.modified').'), '.$db->quoteName('a.modified').', '.$db->quoteName('a.created').') ' . $orderDirn);
				$query->order($db->quoteName('a.id') . ' ' . $orderDirn);
				break;
			default:
				$query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
				break;
		}
		//echo nl2br(str_replace('#__','eburo_',$query));
		return $query;
	}

	public function getItems($is_table = false) {
		$items = parent::getItems($is_table);
		
		foreach ($items as $k => $item) {
			$item->msisdns	= explode(',', $item->msisdns);
			$item->msisdns	= implode('<br/>', array_map(array('GTHelperNumber', 'setMSISDN'), $item->msisdns));
			$item->diff 	= GTHelperDate::diff($item->date);
			$item->date		= JHtml::date($item->date, 'd M Y H:i');

			$items[$k] = $item;
		}

		return $items;
	}

	public function getCategories() {		
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));

		$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('a.user_ids').')');

		$query->where($db->quoteName('a.published').' = 1');

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		$categories = $db->loadObjectList('id');

		return $categories;
	}

	protected function setMSISDN($msisdn) {
		$msisdn = '+'.$msisdn;
		return $msisdn;
	}
}
