<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerConversations extends GTControllerAdmin {
	
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
	public function getModel($name = 'Conversations', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function countMessages() {
		$model = $this->getModel();
		echo $model->countMessages();
		$this->app->close();
	}
	
	public function markRead() {
		$this->applyFunction('updateRead', 'COM_GTSMS_N_ITEMS_MARK_READ', 'Messages');
	}

	public function markUnRead() {
		$this->applyFunction('updateUnread', 'COM_GTSMS_N_ITEMS_MARK_UNREAD', 'Messages');
	}
}
