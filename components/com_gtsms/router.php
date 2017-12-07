<?php

defined('_JEXEC') or die('Restricted access');

function GTSMSBuildRoute(&$query) {
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	
	if (isset($query['Itemid'])) {
		$menuItem = $menu->getItem($query['Itemid']);
	} else {
		$menuItem = $menu->getActive();
	}

	$mQuery		= @$menuItem->query;
	$mView		= @$mQuery['view'];
	$mLayout	= @$mQuery['layout'];
	$qView		= @$query['view'];
	$qLayout	= @$query['layout'];
	$qTask		= @$query['task'];
	$qId		= @$query['id'];

	if($qTask) {
		$segments[] = 'task';
		$segments[] = str_replace('.', '-', $qTask);
	}
	if(in_array($qLayout, array('view', 'edit'))) {
		if($qLayout && $mLayout != $qLayout) {
			$segments[] = $qLayout;
		}
		$segments[] = $qId;
	}

	if(@$query['limit']) {
		$segments[] = 'page';
		if(@$query['limitstart'] == '0') {
			$segments[] = '1';
		} else {
			$start = @$query['limitstart'] ? $query['limitstart'] : $query['start'];
			$page = ($start / $query['limit']) + 1;
			$segments[] = $page;
		}
	}
	
	unset($query['task']);
	unset($query['view']);
	unset($query['layout']);
	unset($query['id']);
	unset($query['limit']);
	unset($query['start']);
	unset($query['limitstart']);
	unset($query['page']);

	return $segments;
}

function GTSMSParseRoute($segments) {
	$i 			= 0;
	$vars		= array();
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$active		= $menu->getActive();
	$query		= @$active->query;
	$mView		= @$query['view'];
	$subView	= array(
		'conversations' => 'messages'
	);
	
	$firstSegment	= @$segments[$i];
	switch($firstSegment) {
		case 'edit':
		case 'view':
			$vars['view']	= in_array($mView, array_keys($subView)) ? $subView[$mView] : GTSMSSingularize($mView);
			$vars['layout']	= $firstSegment; $i++;
			$vars['id']		= @$segments[$i]; $i++;
			break;
		case 'task':
			$i++;
			$task			= @$segments[$i]; $i++;
			$vars['task']	= str_replace(':', '.', $task);
			$vars['id']		= @$segments[$i]; $i++;
			$i++;
			break;
		default:
			$vars['view']	= $mView;
			break;
	}

	if(@$segments[$i] == 'page' && @$segments[$i+1]) {
		$vars['page'] = @$segments[$i+1];
	}

	return $vars;
}

function GTSMSSingularize($word) {
	$rules = array( 
		'ss' => false, 
		'os' => 'o', 
		'ies' => 'y', 
		'xes' => 'x', 
		'oes' => 'oe', 
		'ies' => 'y', 
		'ves' => 'fe', 
		's' => '',
		'eet' => 'oot'
		// if you know more add them here
	);

	foreach( $rules as $key => $v ) {
		// does the word end in a rule?
		if( preg_match( "/".$key."$/" , $word ) ) {
			// we met that ss rule
			if($key === false) {
				return $word;
			}
			// return the word depluraled
			return preg_replace( "/".$key."$/" , $v , $word ); 
		}
	}
	
	// ok we didn't find any rules so return the original word, sorry.... :(
	return $word;
}

?>
