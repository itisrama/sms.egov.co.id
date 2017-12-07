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

jimport('joomla.application.component.controlleradmin');

class GTControllerAdmin extends JControllerAdmin
{
	
	public $app;
	public $context;
	public $contextAction;
	public $user;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context)) {
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r)) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->context = strtolower($r[2]);
		}
		
		// Set variables
		$this->app		= JFactory::getApplication();
		$layout			= $this->app->getUserStateFromRequest($this->context . '.layout', 'layout');
		$this->context2	= implode('.', array($this->option, $layout, $this->context));
		$this->user		= JFactory::getUser();
	}
	
	public function display($cachable = false, $urlparams = false) {
		parent::display($cachable, $urlparams);
	}

	public function applyFunction($function, $message, $model = null) {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		} else {
			// Get the model.
			$model = $model ? $this->getModel($model) : $this->getModel();

			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->$function($cid)) {
				$this->setMessage(JText::plural($message, count($cid)));
			} else {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}
