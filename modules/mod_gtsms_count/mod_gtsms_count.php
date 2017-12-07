<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
// Define DS
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gtsms' . DS . 'helpers' . DS . 'access.php');
//require_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gtsms' . DS . 'helpers' . DS . 'number.php');
require_once (dirname(__FILE__).DS.'helper.php');

$component_path	= 'index.php?option=com_gtsms';
$component_url	= JURI::base(false) . $component_path;
$count_url		= $component_url.'&task=json.countMessages';
$inbox_url		= $component_path.'&view=conversations';
$inbox_url		= $inbox_url.'&Itemid='.modGTSMSCount::getMenuId($inbox_url);
$inbox_url		= JRoute::_($inbox_url);
//$json_url = 'http://smscenter.egov.co.id/sms/status.php';

$document = JFactory::getDocument();
$document->addScriptDeclaration("
// Set variables
	var count_url = '$count_url';
");
JText::script('MOD_GTSMS_COUNT_N_NEW_MESSAGES');
JText::script('MOD_GTSMS_COUNT_LOGIN');

$document->addScript(JURI::root().'modules/mod_gtsms_count/assets/js/update.js');
$document->addStylesheet(JURI::root().'modules/mod_gtsms_count/assets/css/style.css');

$count				= new stdCLass();
$count->new			= modGTSMSCount::countMessages();
$count->received	= modGTSMSCount::countMessages('received');
$count->sent		= modGTSMSCount::countMessages('sent');
$count->failed		= modGTSMSCount::countMessages('failed');
$count->unread		= modGTSMSCount::countMessages('unread');
//echo "<pre>"; print_r($count); echo "</pre>";

$layout	= $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_gtsms_count', $layout);