<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSControllerMessage extends GTControllerForm
{
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->getViewItem($urlQueries = array('id'));
	}

	public function getModel($name = 'Message', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function incoming() {
		// http://sms.egov.co.id/?option=com_gtsms&task=message.incoming&msisdn=[MSISDN]&message=[MESSAGE]&modem=[MODEM]&showid=[SHOWID]
		
		$params		= JComponentHelper::getParams('com_gtsms');
		$model		= $this->getModel();
		$msisdn		= $this->input->get('msisdn', '', false);
		$message	= $this->input->get('message', '', false);
		$modem		= $this->input->get('modem', '', false);
		$showID		= $this->input->get('showid', 0);
		$service	= $model->getService($modem, $message);

		if(@$service->id) {
			$this->input->set('service_id', $service->id);
			$this->input->set('category_id', $service->category_id);

			if($service->remove_keyword) {
				$keywordLen = strlen($service->keyword);
				switch ($service->position) {
					case 'start':
						$message = substr($message, $keywordLen);
						break;
					case 'end':
						$message = substr($message, $keywordLen * -1, $keywordLen); 
						break;
					default:
						$message = str_replace($service->keyword, '', $message);
						break;
				}

				$message = ltrim($message, '#');
				$message = ltrim($message, '_');
				$message = ltrim($message, '*');

				$this->input->set('message', $message);
			}
		}

		if($model->createItem()) {
			if(@$service->id && is_numeric($msisdn) && strlen($msisdn) > 6) {
				$serviceUrl = str_replace(
					array('[MSISDN]', '[MESSAGE]', '[MODEM]'), 
					array($msisdn, urlencode($message), $modem), 
					$service->url
				);

				switch ($service->reply) {
					case 'url':
						$serviceMsg = @file_get_contents($serviceUrl);
						break;
					case 'text':
						$serviceMsg = $service->text;
						break;
					case 'none':
						if($serviceUrl) {
							fopen($serviceUrl, 'r');
						}
						$serviceMsg = null;
						break;
				}
				
				if($serviceMsg) {
					$this->input->set('message', $serviceMsg);
					$this->input->set('date', null);

					$dirOut	= '/home/sms/%s/';
					$dirOut	= sprintf($dirOut, $data->modem);

					if($params->get('is_server')) {
						$model->createItem('process');
						$id = $model->getState('message.id');
						echo $showID ? $serviceMsg.'_____'.$id : $serviceMsg;
					} else {
						$this->outgoing(false);
					}
					
				}
			}
		}

		$this->app->close();
	}

	public function newsms() {
		$model	= $this->getModel();
		$data	= $this->input->post->get('jform', array(), 'array');
		$data	= JArrayHelper::toObject($data);
		
		$this->input->set('message', $data->message);
		$this->input->set('modem', $data->modem);
		$this->input->set('category_id', $data->category_id);

		$msisdns	= array();
		$msisdn_ids	= array();
		foreach ($data->msisdn_ids as $msisdn_id) {
			list($msisdn_id, $type)	= explode(':', $msisdn_id.':');
			switch ($type) {
				case 'id':
					$msisdn_ids[] = $msisdn_id;
					break;
				case 'group':
					$groups_msisdn_ids = GTHelper::getReferences($msisdn_id, 'groups', 'id', 'msisdn_ids');
					$groups_msisdn_ids = reset($groups_msisdn_ids);
					$groups_msisdn_ids = explode(',', $groups_msisdn_ids);
					$msisdn_ids = array_merge($msisdn_ids, $groups_msisdn_ids);
					break;
				default:
					$msisdns[] = $msisdn_id;
					break;
			}
		}
		$msisdn_ids	= array_unique($msisdn_ids);
		$msisdns	= array_unique(array_merge($msisdns, GTHelper::getReferences($msisdn_ids, 'msisdns', 'id', 'msisdn')));
		
		foreach ($msisdns as $msisdn) {
			$this->input->set('msisdn', $msisdn);
			$this->outgoing(false);
		}

		$this->setMessage(sprintf(JText::_('COM_GTSMS_N_MESSAGES_SENT'), count($msisdns)));
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToListAppend(), false
			)
		);
	}

	public function send() {
		$this->outgoing(false);

		$msisdn		= $this->input->get('msisdn', '', false);
		$msisdn_id	= GTHelper::getReferences($msisdn, 'msisdns', 'msisdn', 'id');
		$msisdn_id 	= reset($msisdn_id);

		$this->setMessage(sprintf(JText::_('COM_GTSMS_N_MESSAGES_SENT'), 1));
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list . '&layout=view&id=' . $msisdn_id
				. $this->getRedirectToListAppend(), false
			)
		);
	}

	public function outgoing($close = true) {
		$params	= JComponentHelper::getParams('com_gtsms');
		$model	= $this->getModel();

		if($model->createItem('process')) {
			$sms			= new stdClass();
			$sms->msisdn	= $this->input->get('msisdn', '', false);
			$sms->message	= $this->input->get('message', '', false);
			$sms->modem		= $this->input->get('modem', '');
			$sms->statusUrl	= $this->input->get('url', '', false);
			$sms->id		= $model->getState('message.id');

			if($params->get('is_server')) {
				$this->createOutgoing($sms);
			} else {
				$statusUrl		= GT_GLOBAL_COMPONENT.'&task=message.status&id='.$sms->id.'&type=';
				$outgoingUrl	= $params->get('outgoing_url');
				$outgoingUrl	= str_replace(
					array('[MSISDN]', '[MESSAGE]', '[MODEM]', '[URL]'), 
					array($sms->msisdn, urlencode($sms->message), $sms->modem, urlencode($statusUrl)), 
					$outgoingUrl
				);
				fopen($outgoingUrl, 'r');
			}
		}

		if($close) {
			$this->app->close();
		}
	}

	public function createOutgoing($data) {
		$params	= JComponentHelper::getParams('com_gtsms');
		$dirOut	= $params->get('outgoing_dir');
		$dirOut = rtrim(str_replace(array('/','\\'), DS, $dirOut), DS).DS;
		$dirOut	.= $params->get('multi_modem') ? $data->modem.DS : '';
		$statusUrl	= str_replace(array('/','_'), array('+','-'), base64_encode($data->statusUrl));
		$msgReply	= array();

		if(is_dir($dirOut)) {
			// Send Reply
			$msgReply	= array();
			$msgReply[]	= 'To: '.$data->msisdn;
			$msgReply[]	= '';
			$msgReply[]	= $data->message;
			
			$msgReply	= implode(PHP_EOL, $msgReply);
			$filename	= implode('_____', array($data->id, $statusUrl, $data->modem));
			$filename	= $dirOut.$filename;
			
			// Write Send File
			$handle = fopen($filename, "w");
			fwrite($handle, $msgReply);
			fclose($handle);
		}

		return true;
	}

	public function status() {
		// http://sms.egov.co.id/?option=com_gtsms&task=message.status&id=[ID]&type=[TYPE]&url=[URL]

		$id			= $this->input->get('id');
		$url		= $this->input->get('url', '', false);
		$typeID		= $this->input->get('type', 1);
		$types		= array('failed', 'sent');
		$type		= @$types[$typeID] ? $types[$typeID] : 'sent';
		
		$model		= $this->getModel();
		$message	= $model->getItem($id);

		if($message->id && $message->type != 'received') {
			$message->modified = JFactory::getDate()->toSql();
			$message->type = $type;
			$model->save($message);
		}

		if($url) {
			$url .= $typeID;
			fopen($url, 'r');
		}
		
		$this->app->close();
	}
}
