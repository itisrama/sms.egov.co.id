<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).DS.'..'));
 
require_once (JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once (JPATH_BASE.DS.'includes'.DS.'framework.php' );
require_once 'libphonenumber'.DS.'autoload.php';

function getCallingCode($msisdn) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);

	$prefixes = array();
	$prefixes[] = substr($msisdn, 0, 1);
	$prefixes[] = substr($msisdn, 0, 2);
	$prefixes[] = substr($msisdn, 0, 3);
	$prefixes[] = substr($msisdn, 0, 4);
	$prefixes[] = substr($msisdn, 0, 5);

	array_walk($prefixes, array($db, 'quote'));

	$query->select($db->quoteName(array('a.abbv', 'a.code')));
	$query->from($db->quoteName('#__gtsms_calling_codes', 'a'));
	$query->where($db->quoteName('a.code') . ' IN (' . implode(',', $prefixes) . ')');
	$query->order('CHAR_LENGTH('.$db->quoteName('a.code').')');

	$db->setQuery($query);

	return $db->loadObject();
}

$msisdn 	= $_GET['num'];
$phoneUtil	= \libphonenumber\PhoneNumberUtil::getInstance();
$geocoder	= \libphonenumber\geocoding\PhoneNumberOfflineGeocoder::getInstance();
$carrier	= \libphonenumber\PhoneNumberToCarrierMapper::getInstance();
$callCode	= getCallingCode($msisdn);

if(!@$callCode->abbv) return '+'.$msisdn;

$msisdn		= substr($msisdn, strlen($callCode->code));
$msisdn		= $phoneUtil->parse($msisdn, $callCode->abbv);
$uniqueNum	= $phoneUtil->getNationalSignificantNumber($msisdn);
$destNumLen	= $phoneUtil->getLengthOfNationalDestinationCode($msisdn);

$data = new stdClass();
$data->calling_code	= $msisdn->getCountryCode();
$data->area_code	= substr($uniqueNum, 0, $destNumLen);
$data->msisdn_int	= $phoneUtil->format($msisdn, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);
$data->msisdn_nat	= $phoneUtil->format($msisdn, \libphonenumber\PhoneNumberFormat::NATIONAL);
$data->location		= $geocoder->getDescriptionForNumber($msisdn, "en_US");
$data->carrier		= $carrier->getNameForNumber($msisdn, "en_US");

$json = new stdClass();
$json->result = $data;

header('Content-type: application/json; charset=utf-8');
echo json_encode($json);