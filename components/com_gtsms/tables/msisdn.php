<?php

/**
 * @package		GT SMS
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableMSISDN extends GTTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(&$db) {
		parent::__construct('#__gtsms_msisdns', 'id', $db);
	}
	
	/**
	 * Stores a contact
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false) {		
		$msisdn	= $this->msisdn;

		if($msisdn) {
			$this->type	= is_numeric($msisdn) ? 'numeric' : 'string';
			if(strlen($msisdn) > 8 && $this->type == 'numeric') {
				$params			= JComponentHelper::getParams('com_gtsms');
				$msisdn_url		= $params->get('msisdn_lookup_url');
				$msisdn_url		= str_replace('[MSISDN]', $msisdn, $msisdn_url);
				$msisdn_data	= json_decode(@file_get_contents($msisdn_url));
				$msisdn_data 	= is_object(@$msisdn_data->result) ? $msisdn_data->result : GTHelperNumber::lookupMSISDN($msisdn);

				foreach ($msisdn_data as $k => $item) {
					$this->$k = $item;
				}
			} else {
				$this->msisdn_int = $this->msisdn;
				$this->msisdn_nat = $this->msisdn;
			}
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}
	
	public function bind($array, $ignore = '') {
		$row = JArrayHelper::toObject($array);
		
		if(!$row->id) 
			return parent::bind($array, $ignore);
		
		$array = JArrayHelper::fromObject($row);
		return parent::bind($array, $ignore);
	}
}
