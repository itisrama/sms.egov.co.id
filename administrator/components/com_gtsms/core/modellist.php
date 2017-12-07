<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GTModelList extends JModelList {

	public $app;
	public $input;
	public $user;

	public function __construct($config = array()) {
		parent::__construct($config);

		// Set variables
		$this->app		= JFactory::getApplication();
		$this->input	= $this->app->input;
		$this->user		= JFactory::getUser();

		// Add table path
		$this->addTablePath(GT_TABLES);
	}

	protected function populateState($ordering = null, $direction = null) {
		parent::populateState($ordering, $direction);

		$limit	= $this->getUserStateFromRequest($this->context . '.limit', 'limit', $this->app->getCfg('list_limit'), 'uint');
		$limit	= $limit == 0 || $limit > 50 ? 50 : $limit;
		$this->setState('list.limit', $limit);		
		
		$page	= $this->app->getUserStateFromRequest($this->context . '.page', 'page');
		if($page) {
			$start	= ($page-1) * $limit;
			$this->setState('list.start', $start);
		}
	}

	public function getItems($is_table=false) {
		$items = parent::getItems();
		

		if($is_table) {
			$table = $this->getTable();
			foreach ($items as $k => $item) {
				$table->bind(JArrayHelper::fromObject($item));
				$pk = $table->getProperties(1);
				$items[$k] = JArrayHelper::toObject($pk); 
			}
		}
		
		return $items;
	}

}
