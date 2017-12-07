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

class GTHelperArray
{

	public static function handleNull($array) {
		foreach ($array as $key => $value) {
			$array[$key] = is_null($value) || $value == '' ? '' : $value;
		}
		return $array;
	}

	public static function handleItem($array) {
		foreach ($array as $key => $value) {
			$array[$key] = reset(explode(':', $value));
		}
		return $array;
	}

	public static function toJSON($array, $exclude = array()) {
		if(!count($array)) return null;

		$json = array();
		foreach($array as $k => $fields) {
			foreach ($fields as $field => $value) {
				if(in_array($field, $exclude)) continue;
				$json[$field][$k] = $value; 
			}
		}
		return json_encode($json);
	}

	public static function toFiles($array) {
		$files = array();
		foreach ($array as $field => $names) {
			foreach ($names as $name => $value) {
				if(is_array($value)) {
					foreach ($value as $k => $val) {
						$files[$name][$k][$field] = $val;
					}
				} else {
					$files[$name][$field] = $value;
				}
				
			}
		}
		return JArrayHelper::toObject($files);
	}

	public static function toArray($el) {
		switch(gettype($el)) {
			case 'array':
				$el = $el;
				break;
			case 'object':
				$el = JArrayHelper::fromObject($el);
				break;
			case 'string':
			case 'integer':
			case 'double':
			case 'boolean':
				$el = array($el);
				break;
			default:
				$el = array();
		}

		return $el;
	}

}