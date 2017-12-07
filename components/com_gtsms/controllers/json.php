<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerJson extends GTController
{
	public function __construct($config = array()) {
		$config['load_params'] = false;
		parent::__construct($config);
	}

	public function countMessages() {
		$app		= JFactory::getApplication();
		$session	= JFactory::getSession();
		$user_id	= $session->get('user.id');

		$count			= new stdClass();
		$count->user_id	= $user_id;

		if($user_id) {
			$model				= $this->getModel('Json');
			$count->new			= $model->countMessages();
			$count->received	= $model->countMessages('received');
			$count->sent		= $model->countMessages('sent');
			$count->failed		= $model->countMessages('failed');
			$count->unread		= $model->countMessages('unread');
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($count);

		$app->close();
	}

	public function getModemStatus() {
		$params	= JComponentHelper::getParams('com_gtsms');
		$app	= JFactory::getApplication();

		header('Content-type: application/json; charset=utf-8');

		if($params->get('is_server')) {
			$status = $this->readModemStatus();
			echo json_encode($status);
		} else {
			$statusUrl = $params->get('modem_status_url');
			echo @file_get_contents($statusUrl);
		}

		$app->close();
	}

	public function readModemStatus() {
		$params		= JComponentHelper::getParams('com_gtsms');
		$fileStats	= $params->get('stat_dir');
		$fileStats	= rtrim(str_replace(array('/','\\'), DS, $fileStats), DS).DS.'status';
		$stats		= file($fileStats);
		
		array_shift($stats);

		$modems = array();
		$qualities = array('Marginal', 'Workable', 'Good', 'Excellent');
		foreach($stats as $stat) {
			$stat = preg_split('/\s+/', $stat);
			$datetime = strtotime($stat[1].' '.trim($stat[2], ','));
			$activity = trim($stat[3], ',');
			$quality = str_replace(array('(',')',','), '', $stat[10]);
			$rating = array_search($quality, $qualities);
			$rating = is_numeric($rating) ? $rating+1 : 0;
			$rating = in_array($activity, array('Idle', 'Receiving', 'Sending')) ? $rating : '';

			$modem = new stdClass();
			$modem->name = trim($stat[0], ':');
			$modem->datetime = date('d-m-Y H:i:s', $datetime);
			if((time() - $datetime) < 1*60) {
				$modem->activity = $activity;
				$modem->strength = $stat[8].' '.$stat[9];
				$modem->quality = $quality;
				$modem->rating = $rating;
			} else {
				$modem->activity = 'Service Stopped';
				$modem->strength = 'N/A';
				$modem->quality = 'N/A';
				$modem->rating = '';
			}

			$modems[] = $modem;
		} 

		$json = new stdClass();
		$json->result = $modems;

		return $json;
	}
}
