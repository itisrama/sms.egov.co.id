<?php

/**
 * @package		GT Component 
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSViewContacts extends GTView {

	protected $items;
	protected $pagination;
	protected $state;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	function display($tpl = null) {
		// Get model data.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		$this->editUrl		= GT_COMPONENT.'&task=contact.edit&id=';
		$this->viewUrl		= GT_COMPONENT.'&view=contact&layout=view&id=';

		$this->ordering		= $this->state->get('list.ordering');
		$this->direction	= $this->state->get('list.direction');

		parent::display($tpl);
	}

}
