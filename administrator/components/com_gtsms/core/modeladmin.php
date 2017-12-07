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

jimport('joomla.application.component.modeladmin');

class GTModelAdmin extends JModelAdmin
{
	
	public $app;
	public $input;
	public $context;
	public $prevName;
	public $item;
	public $user;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Set variables
		$this->app		= JFactory::getApplication();
		$this->input	= $this->app->input;
		$this->user		= JFactory::getUser();

		// Adjust the context to support modal layouts.
		$layout = $this->input->get('layout', 'default');
		$this->context	= implode('.', array($this->option, $this->getName(), $layout));

		// Add table path
		$this->addTablePath(GT_TABLES);
	}
	
	protected function populateState() {
		parent::populateState();
	}

	public function getItemExternal($pk = null, $name) {
		$this->name	= $name;
		$return		= JArrayHelper::fromObject(parent::getItem($pk));
		$this->name	= $this->prevName;
		return JArrayHelper::toObject($return);
	}

	protected function loadFormData() {	
		$layout		= $this->app->getUserStateFromRequest($this->getName() . '.layout', 'layout');
		$context	= implode('.', array($this->option, $layout, $this->getName()));
		
		$data		= JFactory::getApplication()->getUserState($context . '.data', array());
		$data		= empty($data) ? $this->item : JArrayHelper::toObject($data);
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true, $control = 'jform') {
		$component_name = $this->input->get('option');
		$model_name = $this->getName();
		
		if($data) {
			$this->item = $data;
		}
		// Get the form.
		$form = $this->loadForm($component_name . '.' . $model_name, $model_name, array('control' => $control, 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		// Modify the form based on access controls.
		if (!$this->canEditState((object)$data)) {
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		
		return $form;
	}

	public function getFormExternal($name, $data = array(), $loadData = true, $control = 'jform') {
		$this->name	= $name;
		$return		= $this->getForm($data, $loadData, $control);
		$this->name	= $this->prevName;
		return $return;
	}

	public function save($data) {
		$data = JArrayHelper::fromObject($data);
		return parent::save($data);
	}

	public function saveExternal($data, $name, $return_id = false) {
		$data	= is_object($data) ? JArrayHelper::fromObject($data) : $data;
		$table	= $this->getTable($name);
		$key	= $table->getKeyName();
		$pk		= intval(@$data[$key]);
		$isNew	= $pk > 0;

		if (!$isNew) {
			$table->load($pk);
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		if (isset($table->$key) && $return_id) {
			return $table->$key;
		} else {
			return true;
		}
	}
	
	public function saveReference($value, $type) {
		$table = GTHelper::pluralize($type);
		$id = $this->getReference($value, $table);
		if($id) {
			return $id;
		} else {
			$data		= new stdClass();
			$data->id	= 0;
			$data->name	= $value;
			return $this->saveExternal($data, $type, true);
		}
	}

	public function saveBulk($items, $table = null) {
		$table = $table ? $table : $this->getName();
		$table = GTHelper::pluralize($table);

		$items = is_object($items) ? JArrayHelper::fromObject($items) : $items;
		if(!count($items) > 0) {
			return true;
		}

		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = reset($items);
		$columns = is_object($columns) ? JArrayHelper::fromObject($columns) : $columns;
		$columns = array_keys($columns);

		foreach ($items as &$item) {
			$item = is_object($item) ? JArrayHelper::fromObject($item) : $item;
			foreach ($item as &$val) {
				$val = $db->quote($val);
			}
			$item = implode(', ', $item);
		}

		// Prepare the insert query.
		$query->insert($db->quoteName('#__gteconomystat_'.$table));
		$query->columns($db->quoteName($columns));
		$query->values($items);

		foreach ($columns as &$column) {
			$column = $db->quoteName($column).' = VALUES('.$db->quoteName($column).')';
		}
		$columns = implode(', ', $columns);

		$query = $query . ' ON DUPLICATE KEY UPDATE ' . $columns;

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);

		return $db->execute();
	}

	public function getReference($value, $type) {
		$table = '#__gtsms_' . $type;
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))->from($table);
		$query->where('(' . $db->quoteName('id') . ' = ' . $db->quote($value) 
			. ' OR LOWER(' . $db->quoteName('name') . ') = LOWER(' . $db->quote($value) . '))');

		$db->setQuery($query);

		return @$db->loadObject()->id;
	}

	public function deleteExternal(&$pks, $name) {
		$this->name	= $name;
		$return		= parent::delete($pks);
		$this->name	= $this->prevName;
		return $return;
	}
}
