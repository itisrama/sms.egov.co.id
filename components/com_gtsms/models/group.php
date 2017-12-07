<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;


class GTSMSModelGroup extends GTModelAdmin
{

	public function __construct($config = array()) {
		parent::__construct($config);

		$tab_position = $this->app->getUserStateFromRequest($this->context . '.tab_position', 'tab_position', 'project');
		$this->setState($this->getName() . '.tab_position', $tab_position);
	}

	protected function populateState() {
		parent::populateState();

		$id = $this->input->getInt('id', 0);
		$this->setState($this->getName().'.id', intval($id));
	}
	
	public function getItem($pk = null) {
		$data = parent::getItem();
		if(!is_object($data)) return false;

		$data->category_ids	= explode(',', $data->category_ids);
		$data->msisdn_ids	= explode(',', $data->msisdn_ids);
		foreach ($data->msisdn_ids as &$msisdn_id) {
			$msisdn_id .= ':id';
		}
		
		$this->item	= $data;

		return $data;
	}

	public function getItemView() {
		$data = parent::getItem();
		if(!is_object($data)) return false;

		$msisdn_ids = explode(',', $data->msisdn_ids);
		foreach ($msisdn_ids as $k => $msisdn_id) {
			$msisdn = $this->getItemExternal($msisdn_id, 'msisdn');
			$msisdn = implode(':', array($msisdn->msisdn, $msisdn->msisdn_nat, $msisdn->msisdn_int, $msisdn->calling_code, $msisdn->area_code));
			$msisdn_ids[$k] = $msisdn;
		}
		$data->msisdn_ids = implode('<br/>', array_map(array('GTHelperNumber', 'setMSISDN'), $msisdn_ids));

		$this->item = $data;

		return $data;
	}

	public function save($data){
		$data = JArrayHelper::toObject($data);

		if(GTHelperAccess::isAdmin()) {
			$category_ids = JArrayHelper::fromObject(@$data->category_ids);
		} else {
			$category_ids = GTHelper::getReferences($data->id, 'groups', 'id', 'category_ids');
			$category_ids = explode(',', reset($category_ids));
		}

		$category_ids = $category_ids ? $category_ids : array();
		$category_ids = array_merge($category_ids, $this->getUserCategoryIDs());

		$msisdn_ids = array();
		foreach ($data->msisdn_ids as $msisdn) {
			$catids					= $category_ids;
			$msisdnRow				= new stdClass();
			list($msisdn, $type)	= explode(':', $msisdn.':');
			switch ($type) {
				case 'id':
					$msisdn_id	= $msisdn;
					$catids2 	= GTHelper::getReferences($msisdn, 'msisdns', 'id', 'category_ids');
					$catids		= array_merge($catids, explode(',', reset($catids2)));
					break;
				default:
					$msisdn		= GTHelperNumber::toMSISDN($msisdn);
					$msisdn_id	= GTHelper::getReferences($msisdn, 'msisdns', 'msisdn', 'id');
					$msisdn_id 	= intval(reset($msisdn_id));

					$msisdnRow->msisdn 	= $msisdn;
					break;
			}

			$msisdnRow->id				= $msisdn_id;
			$msisdnRow->category_ids	= implode(',', array_unique(array_filter($catids)));

			$msisdn_id = $this->saveExternal($msisdnRow, 'msisdn', true);
			$msisdn_ids[] = $msisdn_id;
		}

		$data->category_ids	= implode(',', array_unique($category_ids));
		$data->msisdn_ids = implode(',', array_unique($msisdn_ids));
		if(!parent::save($data)) return false;

		return true;
	}

	public function getUserCategoryIDs() {		
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a.id'));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));
		$query->where('FIND_IN_SET('.$db->quote($this->user->id).', '.$db->quoteName('a.user_ids').')');
		$query->where($db->quoteName('a.published').' = 1');

		//echo nl2br(str_replace('#__','eburo_',$query));

		$db->setQuery($query);
		$category_ids = array_keys($db->loadObjectList('id'));
		return $category_ids ? $category_ids : array();
	}

	public function delete(&$pks) {
		return parent::delete($pks);
	}

	public function fixMSISDN() {
		for($i=1; $i<=375; $i++) {
			$msisdn = $this->getItemExternal($i, 'msisdn');
			if(!$msisdn->id) continue;

			$this->saveExternal($msisdn, 'msisdn');
		}
	}
}
