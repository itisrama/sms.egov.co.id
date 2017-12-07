#!/usr/bin/php
<?php
$dirInc			= '/home/sms/incoming/';
$dirSent		= '/home/sms/sent/';
$dirFailed		= '/home/sms/failed/';
$dirOut			= '/home/sms/modem/%s/';
$incoming_url	= 'http://smscenter.egov.co.id/?option=com_gtsms&task=message.incoming&msisdn=[MSISDN]&message=[MESSAGE]&modem=[MODEM]&date=[DATE]&showid=1';
$status_url		= 'http://smscenter.egov.co.id/?option=com_gtsms&task=message.status&id=[ID]&type=[TYPE]&url=[URL]';

foreach(scandir($dirInc) as $k => $file) {
	if(!is_file($dirInc.$file)) continue;
	$incSMS		= file($dirInc.$file);
	$start_read	= 0;
	$msisdn		= null;
	$modem		= null;
	$message	= array();
	foreach($incSMS as $line) {
		// get mobile number
		if(is_numeric(strpos($line, 'From:'))) {
			$msisdn = trim(str_replace('From: ', '', $line));
		}
		if(is_numeric(strpos($line, 'Modem:'))) {
			$modem = trim(str_replace('Modem: ', '', $line));
		}
		if(is_numeric(strpos($line, 'Received:'))) {
			$date = trim(str_replace('Received: ', '', $line));
		}
		// get message
		if($start_read) {
			$message[] = $line;
		}
		if(is_numeric(strpos($line, 'Length:'))) {
			$start_read = 1;
		}
	}
	
	$message = trim(implode('', $message));
	$sms_url 	= str_replace(
		array('[MSISDN]', '[MESSAGE]', '[MODEM]', '[DATE]'), 
		array(urlencode($msisdn), urlencode($message), urlencode($modem), urlencode($date)), 
		$incoming_url
	);

	$reply = file_get_contents($sms_url, 'r');
	if($reply && strlen($reply) < 1000) {
		list($reply, $id)	= explode('_____', $reply.'_____');

		$id				= intval($id);
		$url			= '';
		$msgReply		= array();
		$dirOutReply	= sprintf($dirOut, $modem);

		if(is_dir($dirOutReply)) {
			// Send Reply
			$msgReply	= array();
			$msgReply[]	= 'To: '.$msisdn;
			$msgReply[]	= '';
			$msgReply[]	= $reply;
			
			$msgReply	= implode(PHP_EOL, $msgReply);
			$filename	= implode('_____', array($id, $url, $modem));
			$filename	= $dirOutReply.$filename;

			// Write Send File
			$handle	= fopen($filename, "w");
			fwrite($handle, $msgReply);
			fclose($handle);
		}
	}

	unlink($dirInc.$file);
}


foreach(scandir($dirSent) as $file) {
	if(!is_file($dirSent.$file)) continue;

	updateStatus($file, $status_url, 1);
	unlink($dirSent.$file);
}

foreach(scandir($dirFailed) as $file) {
	if(!is_file($dirFailed.$file)) continue;

	updateStatus($file, $status_url, 0);

	$filename		= explode('_____', $file);
	$modem			= @$filename[2];
	$try			= intval(@$filename[3]);
	$dirOutFailed	= sprintf($dirOut, $modem);

	if($try < 5 && is_dir($dirOutFailed)) {
		rename($dirFailed.$file, $dirOutFailed.$file.'_____'.$try+1);
	} else {
		unlink($dirFailed.$file);
	}
}

function updateStatus($file, $status_url, $status = 1) {
	$file		= explode('_____', $file);
	$id			= @$file[0];
	$url		= @$file[1];
	$url		= base64_decode(str_replace(array('+','-'), array('/','_'), $url));

	if($id) {
		$sms_url 	= str_replace(
			array('[ID]', '[TYPE]', '[URL]'), 
			array($id, $status, urlencode($url)), 
			$status_url
		);
		fopen($sms_url, 'r');
	}
}