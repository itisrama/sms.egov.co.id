<?php

/**
 * @package		GT JSON
 * @author		Herwin Pradana
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2014 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerRef_Offices extends GTControllerAdmin {
	public function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Ref_Office', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
