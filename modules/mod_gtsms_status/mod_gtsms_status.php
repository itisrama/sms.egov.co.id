<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
// Define DS
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');
require_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gtsms' . DS . 'helpers' . DS . 'number.php');

$json_url = JURI::base(false) . '?option=com_gtsms&task=json.getModemStatus';
//$json_url = 'http://smscenter.egov.co.id/sms/status.php';

$document = JFactory::getDocument();
$document->addScriptDeclaration("
// Set variables
	var json_url = '$json_url';
");
$document->addScript(JURI::root().'modules/mod_gtsms_status/assets/js/script.js');
$document->addStylesheet(JURI::root().'modules/mod_gtsms_status/assets/css/style.css');


$modems = modGTSMSStatus::getModems();

$layout	= $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_gtsms_status', $layout);