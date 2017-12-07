<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerSelectize extends GTControllerAdmin {
	
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
	public function getModel($name = 'Selectize', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function getItem() {
		$table	= GTHelper::pluralize($this->input->get('table'));
		$name	= $this->input->get('name_field', 'name');
		$key	= $this->input->get('key_field', 'id');
		$model	= $this->getModel();
		$items	= $model->searchItem($table, $key, $name);
		echo json_encode($items);
		$this->app->close();
	}
	
	public function getMSISDN() {
		$model	= $this->getModel();
		$items	= $model->searchMSISDN();
		echo json_encode($items);
		$this->app->close();
	}

	public function getContact() {
		$model	= $this->getModel();
		$items	= $model->searchContact();
		echo json_encode($items);
		$this->app->close();
	}

	public function getGroup() {
		$model	= $this->getModel();
		$items	= $model->searchGroup();
		echo json_encode($items);
		$this->app->close();
	}

	public function getContactAndGroup() {
		$model	= $this->getModel();
		$items	= $model->searchContactAndGroup();
		echo json_encode($items);
		$this->app->close();
	}
}
