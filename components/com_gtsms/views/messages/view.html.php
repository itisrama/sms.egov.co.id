<?php

/**
 * @package		GT Component 
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSViewMessages extends GTView {

	protected $items;
	protected $pagination;
	protected $state;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	function display($tpl = null) {
		if($this->input->get('json')) {
			// Get model data.
			$this->items = $this->get('Items');

			echo json_encode($this->items);
			$this->app->close();
		} else {			
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
			
			$this->isTrashed	= $this->input->get('published') == -2;
			$this->msisdn		= $this->get('MSISDN');
			$this->modems		= $this->get('Modems');
			$this->categories	= $this->get('Categories');
			$this->contact		= $this->msisdn->contact_name ? $this->msisdn->contact_name.' ('.$this->msisdn->msisdn.')' : $this->msisdn->msisdn;
			
			$this->ordering		= $this->state->get('list.ordering');
			$this->direction	= $this->state->get('list.direction');
			
			// Add pathway
			$pathway	= $this->app->getPathway();
			$pathway->addItem($this->contact);
			
			$this->get('UpdateRead');
			
			parent::display($tpl);
		}
	}

}
