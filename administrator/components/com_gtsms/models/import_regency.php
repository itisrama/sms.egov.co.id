<?php

/**
 * @package		GT JSON
 * @author		Herwin Pradana
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2014 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;


class GTSMSModelImport_Regency extends GTModelAdmin{

	protected function populateState() {
		parent::populateState();
	}
	
	public function getItem($pk = null) {
		return null;
	}

	public function save($data, $return_num = false) {
		$data		= JArrayHelper::toObject($data);

		$return = parent::save($data);
		return $return;
	}
}
