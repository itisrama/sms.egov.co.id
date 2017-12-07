<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;


class GTSMSModelMessage extends GTModelAdmin
{

	public function __construct($config = array()) {
		parent::__construct($config);

		$tab_position = $this->app->getUserStateFromRequest($this->context . '.tab_position', 'tab_position', 'project');
		$this->setState($this->getName() . '.tab_position', $tab_position);
	}

	protected function populateState() {
		parent::populateState();

		$id = $this->input->getInt('id', 0);
		$this->setState('message.id', intval($id));
	}
	
	public function getItem($pk = null) {
		$data		= parent::getItem($pk);
		if(!is_object($data)) return false;
		$this->item	= $data;

		return $data;
	}

	public function getItemView() {
		$data		= parent::getItem();
		if(!is_object($data)) return false;
		$this->item	= $data;

		return $data;
	}

	public function getService($modem = null, $message = null) {
		$modem		= $modem ? $modem : $this->input->get('modem', '', false);
		$message	= $message ? $message : $this->input->get('message', '', false);
		$message	= strtolower($message);

		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.category_id', 'a.text', 'a.url', 'a.keyword', 'a.reply', 'a.position', 'a.remove_keyword')));
		$query->from($db->quoteName('#__gtsms_services', 'a'));

		$query->where('FIND_IN_SET('.$db->quote($modem).', '.$db->quoteName('a.modems').')');
		
		$wheres		= array();
		$wheres[]	= '('.$db->quoteName('a.position').' = '.$db->quote('start').' AND '.$db->quote($message).' LIKE CONCAT(LOWER('.$db->quoteName('a.keyword').'),"%"))';
		$wheres[]	= '('.$db->quoteName('a.position').' = '.$db->quote('end').' AND '.$db->quote($message).' LIKE CONCAT("%", LOWER('.$db->quoteName('a.keyword').')))';
		$wheres[]	= '('.$db->quoteName('a.position').' = '.$db->quote('any').' AND '.$db->quote($message).' LIKE CONCAT("%", LOWER('.$db->quoteName('a.keyword').'),"%"))';
		$wheres[]	= 'TRIM('.$db->quoteName('a.keyword').') = ""';
		$query->where('('.implode(' OR ', $wheres).')');

		$query->order('IF(TRIM('.$db->quoteName('a.keyword').') = "", 1, 0)');
		$query->order($db->quoteName('a.id'));
		$query->limit(1);

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function save($data){
		$data = is_object($data) ? $data : JArrayHelper::toObject($data);
		
		if(!parent::save($data)) return false;

		return true;
	}

	public function delete(&$pks) {
		return parent::delete($pks);
	}

	public function createItem($type = "received", $msisdn = null, $message = null, $modem = null, $date = null) {
		$msisdn			= $msisdn ? $msisdn : $this->input->get('msisdn', '', false);
		$msisdn			= trim($msisdn);
		$message		= $message ? $message : $this->input->get('message', '', false);
		$message		= trim($message);
		$modem			= $modem ? $modem : $this->input->get('modem', '', false);
		$date			= $date ? $date : $this->input->get('date', '', false);
		$service_id		= $this->input->get('service_id', 0);
		$category_id	= $this->input->get('category_id', 0);

		if(!($msisdn && $message && $modem)) {
			return false; 
		}

		$msisdn		= GTHelperNumber::toMSISDN($msisdn);
		$msisdn_id	= GTHelper::getReferences($msisdn, 'msisdns', 'msisdn', 'id');
		$msisdn_id	= intval(reset($msisdn_id));

		$d_msisdn		= new stdClass();
		$d_msisdn->id	= $msisdn_id;

		if($msisdn_id) {
			$category_ids			= GTHelper::getReferences($msisdn_id, 'msisdns', 'id', 'category_ids');
			$d_msisdn->category_ids	= array_merge(array($category_id), explode(',', reset($category_ids)));
		} else {
			$d_msisdn->category_ids	= array($category_id);
			$d_msisdn->msisdn 		= $msisdn;
		}
		$d_msisdn->category_ids = implode(',', array_unique(array_filter($d_msisdn->category_ids)));

		$msisdn_id	= $this->saveExternal($d_msisdn, 'msisdn', true);
		$modem_id	= GTHelper::getReferences($modem, 'modems', 'name', 'id');
		$modem_id 	= reset($modem_id);
		if($modem && !$modem_id) {
			$d_modem		= new stdClass();
			$d_modem->id	= 0;
			$d_modem->name	= $modem;
			$this->saveExternal($d_modem, 'modem');
		}

		$data					= new stdCLass();
		$data->id				= 0;
		$data->msisdn_id		= $msisdn_id;
		$data->message			= urldecode($message);
		$data->modem			= $modem;
		$data->type				= $type;
		$data->service_id		= $service_id;
		$data->category_id		= $category_id;
		$data->created			= $date ? GTHelperDate::userToGMT($date) : JFactory::getDate()->toSql();

		$data = JArrayHelper::fromObject($data);
		$this->save($data);

		$message_id = $this->getState('id');

		return true;
	}
}
