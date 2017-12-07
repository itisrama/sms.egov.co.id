<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerMessages extends GTControllerAdmin {
	
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
	public function getModel($name = 'Message', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function markRead() {
		$this->applyFunction('updateRead', '');
	}

	public function markUnRead() {
		$this->applyFunction('updateUnread', '');
	}

	public function publish() {
		parent::publish();

		$formUrl = JRoute::_('index.php?option=com_gtsms&view=messages&layout=view&id='. $this->input->get('id'));
		$this->setRedirect($formUrl);
	}

	public function delete() {
		parent::delete();

		$formUrl = JRoute::_('index.php?option=com_gtsms&view=messages&layout=view&id='. $this->input->get('id'));
		$this->setRedirect($formUrl);
	}

	public function back($toItem = false) {
		// set layout to view layout
		$this->input->set('layout', 'view');

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=conversations'
			)
		);
	}

	public function changeCategory() {
		$model = $this->getModel('Messages');
		$model->changeCategory();

		$pks = $this->input->get('cid', array(), 'array');
		$this->setMessage(sprintf(JText::_('COM_GTSMS_N_CATEGORY_CHANGED'), count($pks)));

		$formUrl = JRoute::_('index.php?option=com_gtsms&view=messages&layout=view&id='. $this->input->get('id'));
		$this->setRedirect($formUrl);
	}
}
