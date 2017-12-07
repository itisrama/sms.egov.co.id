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

class GTHelper {

	public function getInfo() {
		$xml = JPATH_COMPONENT_ADMINISTRATOR . DS . 'manifest.xml';
		$xml = JApplicationHelper::parseXMLInstallFile($xml);

		$info = new stdClass();
		$info->name			= $xml['name'];
		$info->type			= $xml['type'];
		$info->creationDate	= $xml['creationdate'];
		$info->creationYear	= array_pop(explode(' ', $xml['creationdate']));
		$info->author		= $xml['author'];
		$info->copyright	= $xml['copyright'];
		$info->authorEmail	= $xml['authorEmail'];
		$info->authorUrl	= $xml['authorUrl'];
		$info->version		= $xml['version'];
		$info->description	= $xml['description'];

		return $info;
	}
	
	public static function pluralize($word) {
		$plural = array(
			array('/(x|ch|ss|sh)$/i', "$1es"),
			array('/([^aeiouy]|qu)y$/i', "$1ies"),
			array('/([^aeiouy]|qu)ies$/i', "$1y"),
			array('/(bu)s$/i', "$1ses"),
			array('/s$/i', "s"),
			array('/$/', "s"));

		// Check for matches using regular expressions
		foreach ($plural as $pattern)
		{
			if (preg_match($pattern[0], $word))
			{
				$word = preg_replace($pattern[0], $pattern[1], $word);
				break;
			}
		}
		return $word;
	}

	public function recursive_ksort(&$array) {
	    foreach ($array as $k => $v) {
	        if (is_array($v)) {
	            self::recursive_ksort($v);
	        }
	    }
	    return ksort($array);
	}

	public static function getMenuId($url) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id')->from('#__menu')->where($db->quoteName('link') .' = '.$db->quote($url));

		$db->setQuery($query);
		return intval(@$db->loadObject()->id);
	}
	
	
	public static function addSubmenu($vName) {
		JHtmlSidebar::addEntry(
			sprintf(JText::_('COM_GTSMS_TITLE_IMPORT_EXCEL'), JText::_('COM_GTSMS_TITLE_REGENCY')),
			'index.php?option=com_gtsms&amp;view=import_regency',
			$vName == 'import_regency'
		);
	}

	public static function cleanstr($str) {
		return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $str));
	}

	public static function fixJSON($str) {
		$str = preg_replace("/(?<!\"|'|\w)([a-zA-Z0-9_]+?)(?!\"|'|\w)\s?:/", "\"$1\":", $str);
		$str = str_replace("'", '"', $str);

		return $str;
	}

	public static function getReferences($pks, $table, $key = 'id', $name = 'name', $published = null, $index = 'id') {
		$pks = GTHelperArray::toArray($pks);

		if(!count($pks) > 0) return array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		array_walk($pks, array($db, 'quote'));

		foreach ($pks as $k => $pk) {
			$pks[$k] = $db->quote($pk); 
		}

		$query->select($db->quoteName(array('a.'.$name, 'a.'.$key)));
		$query->from($db->quoteName('#__gtsms_'.$table, 'a'));
		$query->where($db->quoteName('a.'.$key) . ' IN (' . implode(',', $pks) . ')');

		if(is_numeric($published)) {
			$query->where($db->quoteName('a.published') . ' = ' . $db->quote($published));
		}

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','eburo_',$query));
		
		$items = $db->loadObjectList($index);

		foreach ($items as &$item) {
			$item = $item->$name;
		}

		return $items ? $items : array();
	}
}
