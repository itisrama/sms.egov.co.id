<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSModelSelectize extends GTModelList
{
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	
	public function __construct($config = array()) {	
		parent::__construct($config);
	}
	
	public function searchItem($table = null, $key = null, $text = null) {
		// Get a db connection.
		$db		= $this->_db;
		
		// Create a new query object.
		$query	= $db->getQuery(true);
		
		// Select fields from main table
		$query->select($db->quoteName('a.'.$key, 'id'));
		$query->select($db->quoteName('a.'.$text, 'name'));
		$query->select($db->quoteName('a.'.$text, 'label'));
		$query->from($db->quoteName('#__gtsms_'.$table, 'a'));
		
		// Filter search
		$search	= $this->input->get('search');
		$ids	= array_filter($this->input->get('ids', array(), 'array'));

		if(count($ids)>0) {
			$query->where($db->quoteName('a.'.$key) . 'IN (' . implode(',', $ids) .')');
		} elseif (!empty($search)) {
			
			// If contains spaces, the words will be used as keywords.
			if (preg_match('/\s/', $search)) {
				$search = str_replace(' ', '%', $search);
			}
			$search			= $db->quote('%' . $search . '%');
			
			$search_query	= array();
			$search_query[]	= $db->quoteName('a.'.$text) . 'LIKE ' . $search;
			$query->where('(' . implode(' OR ', $search_query) . ')');
		}

		$query->order($db->escape('RAND()'));

		$data = $this->_getList($query, 0, 10);

		//echo nl2br(str_replace('#__','eburo_',$query));
		return $data;
	}

	public function searchUser() {
		// Get a db connection.
		$db		= $this->_db;
		
		// Create a new query object.
		$query	= $db->getQuery(true);
		
		// Select fields from main table
		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->select($db->quoteName('a.name', 'label'));
		$query->from($db->quoteName('#__users', 'a'));

		// Is join reference user
		$all_user	= $this->input->get('all_user');
		if($all_user == 'false'){
    		$free_user	= $this->input->get('free_user');
		    if($free_user == true){
			    $query->join('LEFT OUTER', $db->quoteName('#__gtsms_ref_users', 'b') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.user_id'));
		    }
		    else{
			    $query->join('INNER', $db->quoteName('#__gtsms_ref_users', 'b') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.user_id'));
			}

			$role	= $this->input->get('role');
			if($role) {
				$query->where($db->quoteName('b.role') . ' = "' .$role. '"');
			}
		}
		
		// Filter search
		$search		= $this->input->get('search');
		if (!empty($search)) {
			
			// If contains spaces, the words will be used as keywords.
			if (preg_match('/\s/', $search)) {
				$search = str_replace(' ', '%', $search);
			}
			$search			= $db->quote('%' . $search . '%');
			
			$search_query	= array();
			$search_query[]	= $db->quoteName('a.name') . 'LIKE ' . $search;
			$query->where('(' . implode(' OR ', $search_query) . ')');
		}

		$query->order($db->escape('RAND()'));

		$data = $this->_getList($query, 0, 10);

		//echo nl2br(str_replace('#__','eburo_',$query));
		return $data;
	}

	public function searchMSISDN() {
		// Get a db connection.
		$db		= $this->_db;
		
		// Create a new query object.
		$query	= $db->getQuery(true);
		
		// Select fields from main table
		$query->select($db->quoteName(array('a.id')));
		$query->select('CONCAT_WS(":",`a`.`msisdn`,`a`.`msisdn_nat`,`a`.`msisdn_int`, `a`.`calling_code`,`a`.`area_code`, REPLACE(`a`.`msisdn_nat`, "-", "")) msisdn');
		$query->from($db->quoteName('#__gtsms_msisdns', 'a'));
		
		// Filter search
		$search	= $this->input->get('search');
		$ids	= $this->input->get('ids', array(), 'array');

		$params			= JComponentHelper::getParams('com_gtsms');
		$country_code	= $params->get('def_calling_code', '62');
		if(count($ids)>0) {
			$msisdn_ids = array($db->quote('-9999'));
			foreach ($ids as $id) {
				list($msisdn_id, $type) = explode(':', $id);
				if($type != 'id') {
					continue;
				}
				$msisdn_ids[] = $db->quote($msisdn_id);
			}
			$query->where($db->quoteName('a.id') . 'IN (' . implode(',', $msisdn_ids) .')');
		} else {
			$query->join('LEFT', $db->quoteName('#__gtsms_contacts', 'b') . ' ON FIND_IN_SET(' . $db->quoteName('a.id') . ', ' . $db->quoteName('b.msisdn_ids') . ')');

			if (!empty($search)) {
				// If contains spaces, the words will be used as keywords.
				if (preg_match('/\s/', $search)) {
					$search = str_replace(' ', '%', $search);
				}
				$search			= $db->quote('%' . $search . '%');
				
				$search_query	= array();
				$search_query[]	= 'CONCAT_WS(":",`a`.`msisdn`,`a`.`msisdn_nat`, `a`.`msisdn_int`, REPLACE(`a`.`msisdn_nat`, "-", "")) LIKE ' . $search;
				$query->where('(' . implode(' OR ', $search_query) . ')');
			}

			$query->where($db->quoteName('a.calling_code') . ' <> ""');
			$query->where($db->quoteName('b.id') . ' IS NULL');
		}

		if(!GTHelperAccess::isAdmin($this->input->get('user_id'))) {
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'c') . ' ON FIND_IN_SET('.$db->quoteName('c.id').', '.$db->quoteName('a.category_ids').')');
			$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('c.user_ids').')');
		}

		$query->group($db->quoteName('a.id'));
		$query->order($db->escape('RAND()'));
		//echo nl2br(str_replace('#__','eburo_',$query)); die;
		$items = $this->_getList($query, 0, 10);
		foreach ($items as &$item) {
			$item->label	= GTHelperNumber::setMSISDN($item->msisdn);
			$item->name		= $item->msisdn;
			$item->id		.= ':id'; 
		}
		
		return $items;
	}

	public function searchContact() {
		// Get a db connection.
		$db		= $this->_db;
		
		// Create a new query object.
		$query	= $db->getQuery(true);
		
		// Select fields from main table
		$query->select($db->quoteName(array('a.id')));
		$query->select('CONCAT_WS(":",`a`.`msisdn`,`a`.`msisdn_nat`,`a`.`msisdn_int`, `a`.`calling_code`,`a`.`area_code`, REPLACE(`a`.`msisdn_nat`, "-", "")) msisdn');
		$query->from($db->quoteName('#__gtsms_msisdns', 'a'));
		
		// Filter search
		$search	= $this->input->get('search');
		$ids	= $this->input->get('ids', array(), 'array');
		
		$params			= JComponentHelper::getParams('com_gtsms');
		$country_code	= $params->get('def_calling_code', '62');
		
		$query->select($db->quoteName(array('b.name', 'b.category_ids')));
		$query->join('LEFT', $db->quoteName('#__gtsms_contacts', 'b') . ' ON FIND_IN_SET(' . $db->quoteName('a.id') . ', ' . $db->quoteName('b.msisdn_ids') . ')');

		if(count($ids)>0) {
			$msisdn_ids = array($db->quote('-9999'));
			foreach ($ids as $id) {
				list($msisdn_id, $type) = explode(':', $id);
				if($type != 'id') {
					continue;
				}
				$msisdn_ids[] = $db->quote($msisdn_id);
			}
			$query->where($db->quoteName('a.id') . 'IN (' . implode(',', $msisdn_ids) .')');
		} else {
			if (!empty($search)) {
				// If contains spaces, the words will be used as keywords.
				if (preg_match('/\s/', $search)) {
					$search = str_replace(' ', '%', $search);
				}
				$search			= $db->quote('%' . $search . '%');
				
				$search_query	= array();
				$search_query[]	= $db->quoteName('b.name').' LIKE ' . $search;
				$search_query[]	= 'CONCAT_WS(":",`a`.`msisdn`,`a`.`msisdn_nat`, `a`.`msisdn_int`, REPLACE(`a`.`msisdn_nat`, "-", "")) LIKE ' . $search;
				$query->where('(' . implode(' OR ', $search_query) . ')');
			}
		}

		$query->where($db->quoteName('a.calling_code') . ' <> ""');
		$query->where($db->quoteName('a.calling_code'));

		if(!GTHelperAccess::isAdmin($this->input->get('user_id'))) {
			$categories = array_keys($this->getCategories());
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'c') . ' ON FIND_IN_SET('.$db->quoteName('c.id').', '.$db->quoteName('a.category_ids').')');
			$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('c.user_ids').')');
		}

		$query->group($db->quoteName('a.id'));
		$query->order($db->escape('RAND()'));

		//echo nl2br(str_replace('#__','eburo_',$query));
		$items = $this->_getList($query, 0, 10);
		foreach ($items as &$item) {
			if(!GTHelperAccess::isAdmin($this->input->get('user_id'))) {
				$item->name = array_intersect($categories, explode(',', $item->category_ids)) ? $item->name : null;
			}
			
			$item->label	= GTHelperNumber::setMSISDN($item->msisdn);
			$item->label	= $item->name ? $item->name.' ('.$item->label.')' : $item->label;
			$item->name		= $item->name.$item->msisdn;
			$item->id		.= ':id'; 
		}
		
		return $items;
	}

	public function searchGroup() {
		// Get a db connection.
		$db		= $this->_db;
		
		// Create a new query object.
		$query	= $db->getQuery(true);

		// Select fields from main table
		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_groups', 'a'));
		
		// Filter search
		$search	= $this->input->get('search');
		$ids	= $this->input->get('ids', array(), 'array');
		
		if(count($ids)>0) {
			$group_ids = array($db->quote('-9999'));
			foreach ($ids as $id) {
				list($group_id, $type) = explode(':', $id);
				if($type != 'group') {
					continue;
				}
				$group_ids[] = $db->quote(reset($group_id));
			}
			$query->where($db->quoteName('a.id') . 'IN (' . implode(',', $group_ids) .')');
		} else {
			if (!empty($search)) {
				// If contains spaces, the words will be used as keywords.
				if (preg_match('/\s/', $search)) {
					$search = str_replace(' ', '%', $search);
				}
				$search			= $db->quote('%' . $search . '%');
				
				$search_query	= array();
				$search_query[]	= $db->quoteName('a.name').' LIKE ' . $search;
				$query->where('(' . implode(' OR ', $search_query) . ')');
			}
		}

		if(!GTHelperAccess::isAdmin($this->input->get('user_id'))) {
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'c') . ' ON FIND_IN_SET('.$db->quoteName('c.id').', '.$db->quoteName('a.category_ids').')');
			$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('c.user_ids').')');
		}

		$query->group($db->quoteName('a.id'));
		$query->order($db->escape('RAND()'));

		//echo nl2br(str_replace('#__','eburo_',$query));
		$items = $this->_getList($query, 0, 10);
		foreach ($items as &$item) {
			$item->label	= strtoupper(JText::_('COM_GTSMS_FIELD_GROUP')).' : '.$item->name;
			$item->id		.= ':group'; 
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

	public function searchContactAndGroup() {
		$groups		= $this->searchGroup();
		$contacts	= $this->searchContact();

		$items = array_merge($groups, $contacts);

		return $items;
	}
}
