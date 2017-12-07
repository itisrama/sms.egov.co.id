<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */

defined('_JEXEC') or die;

class GTSMSViewMessage extends GTView {

	public $item;
	public $itemView;
	public $form;
	public $state;
	public $canDo;
	public $params;
	public $buttons;
	public $item_title;

	public function ___construct($config = array()) {
		parent::__construct($config);
	}

	public function display($tpl = null) {
		// Get model data.
		$this->state		= $this->get('State');
		$this->params		= $this->state->params;
		
		$layout 			= $this->getLayout();
		switch($layout) {
			case 'view':
				$this->item 		= $this->get('ItemView');
				$this->form			= $this->get('Form');
				
				break;
			default:
				$this->item			= $this->get('Item');
				$this->form			= $this->get('Form');

				break;
		}
		
		// Set page title
		$this->page_title = JText::_('COM_GTSMS_PT_NEW_MESSAGE');
		GTHelperHTML::setTitle($this->page_title);
		
		// Assign additional data
		$this->canDo = GTHelperAccess::getActions();
		
		// Add pathway
		$pathway	= $this->app->getPathway();
		$pathway->addItem($this->page_title);
		
		// Check permission and display
		$created_by	= isset($this->item->created_by) ? $this->item->created_by : 0;
		GTHelperAccess::checkPermission($this->canDo, $created_by);

		parent::display($tpl);
	}

}
