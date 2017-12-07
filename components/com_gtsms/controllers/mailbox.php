<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerMailbox extends GTControllerAdmin {
	
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

	public function changeCategory() {
		$model = $this->getModel();
		$model->changeCategory();

		$pks = $this->input->get('cid', array(), 'array');
		$this->setMessage(sprintf(JText::_('COM_GTSMS_N_CATEGORY_CHANGED'), count($pks)));

		$formUrl = JRoute::_('index.php?option=com_gtsms&view=mailbox');
		$this->setRedirect($formUrl);
	}
}
