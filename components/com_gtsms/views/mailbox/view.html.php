<?php

/**
 * @package		GT Component 
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSViewMailbox extends GTView {

	protected $items;
	protected $pagination;
	protected $state;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	function display($tpl = null) {
		$this->items	= $this->get('Items');

		if($this->input->get('json')) {
			echo json_encode($this->items);
			$this->app->close();
		} else {
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
			
			$this->modems		= $this->get('Modems');
			$this->categories	= $this->get('Categories');

			$this->ordering		= $this->state->get('list.ordering');
			$this->direction	= $this->state->get('list.direction');
			
			parent::display($tpl);
		}
	}

}
