<?php

/**
 * @package		GT Component 
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSViewDashboard extends GTView {
	
	protected $items;
	protected $pagination;
	protected $state;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->state		= $this->get('State');
		$this->categories	= $this->get('Categories');
		$this->modems		= $this->get('Modems');
		
		parent::display($tpl);
	
	}

}
