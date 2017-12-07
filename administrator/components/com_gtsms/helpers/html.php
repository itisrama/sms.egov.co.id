<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class GTHelperHTML
{
	
	static function loadHeaders() {
		$document = JFactory::getDocument();
		// Add Styles
		$document->addStylesheet(GT_GLOBAL_CSS . '/style.css');

		// Add Scripts
		$document->addScript(GT_ADMIN_JS . '/jquery.min.js');
		$document->addScript(GT_GLOBAL_JS . '/script.js');
		$document->addScript(GT_ADMIN_JS . '/script.js');

		// Set JS Variables
		$component_url = GT_GLOBAL_COMPONENT;
		$assets_url = GT_GLOBAL_ASSETS;
		$document->addScriptDeclaration("
		// Set variables
			var component_url = '$component_url';
			var assets_url = '$assets_url';
		");

		// Set translation constant to JS
		JText::script('ERROR');
		JText::script('WARNING');
		JText::script('SUCCESS');
		JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		JText::script('COM_GTSMS_CONFIRM_DELETE');
		
		$document->addScript(GT_ADMIN_JS . '/jquery-sortable-min.js');
	}
	
	static function setTitle($title = '') {
		$app = JFactory::getApplication();
		$position = $app->getCfg('sitename_pagetitles');
		$document = JFactory::getDocument();
		switch ($position) {
			case 1:
				$document->setTitle($app->getCfg('sitename') . ' - ' . $title);
				break;
			case 2:
				$document->setTitle($title . ' - ' . $app->getCfg('sitename'));
				break;
			default:
				$document->setTitle($title);
				break;
		}
	}

	static function gridSort($name, $field, $ordering, $direction) {
		$search		= array('icon-arrow-up-3', 'icon-arrow-down-3');
		$replace	= array('fa fa-caret-up', 'fa fa-caret-down');
		$gridSort	= JHtml::_('grid.sort', $name, $field, $direction, $ordering);

		return str_replace($search, $replace, $gridSort);
	}

	static function getDropdown($name, $label, $task, $options, $type='default', $isSpan=true, $isList=true, $default='') {
		$label = JText::_($label);
		$html = array();

		$html[] = '<button class="btn btn-'.$type.' dropdown-toggle" type="button" data-toggle="dropdown">'.$label.' <span class="caret"></span></button>';
		$html[] = '<div style="display:none">';
		$html[] = '<input type="hidden" class="task" value="'.$task.'" />';
		$html[] = '<input type="hidden" class="is_list" value="'.$isList.'" />';
		$html[] = '<input type="hidden" class="input" name="'.$name.'" value="'.$default.'" />';
		$html[] = '</div>';
		$html[] = '<ul class="dropdown-menu">';
		foreach ($options as $k => $option) {
			if(is_object($option)) {
				$value = $option->id;
				$label = $option->name;
			} else {
				$value = $k;
				$label = $option;
			}
			$html[] = '<li><a class="option" val="'.$value.'">'.$label.'</a></li>';
		}
		$html[] = '</ul>';

		return $isSpan? '<span class="dropdown">'.implode('', $html).'</span>' : implode('', $html);
	}

	static function getSelectize($name, $value, $query, $class = null, $requests = null, $task = 'selectize.getItems', $attr = null) {
		$db		= JFactory::getDBO();
		
		$id			= $name;
		
		$db->setQuery(str_replace('%s', '"'.implode('","', $value).'"', $query));
		$items 		= $db->loadObjectlist();
		$options	= array();
		
		if ($items) {
			foreach ($items as $item) {
				$options[] = JHtml::_('select.option', $item->id, $item->name);
			}
		}
		
		// Merge any additional options in the XML definition.
		$options	= array_merge(parent::getOptions(), $options);
		
		// Load JSs
		$document	= JFactory::getDocument();
		$document->addScript(GT_ADMIN_JS . '/selectize.min.js');
		$document->addStylesheet(GT_ADMIN_CSS . '/selectize.bootstrap3.css');;
		
		$component_url = GT_GLOBAL_COMPONENT;

		$script		= "
			(function ($){
				$(document).ready(function (){
					$('#$id').selectize({
						persist: false,
						valueField: 'id',
						labelField: 'name',
						searchField: 'name',
						sortField: 'name',
						create: $create,
						preload: true,
						load: function(query, callback) {
							data = $requests;
							data.search = query;
							data.task = '$task';
							$.ajax({
								url: '$component_url',
								data: data,
								type: 'GET',
								error: function() {
									callback();
								},
								success: function(result) {
									callback($.parseJSON(result));
								}
							});
						},
					});
				});
			})(jQuery);
		";
		$document->addScriptDeclaration($script);

		return JHtml::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
	}
}
