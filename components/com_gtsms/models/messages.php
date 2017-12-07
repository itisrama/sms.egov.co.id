<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSModelMessages extends GTModelList
{
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */

	protected $modems;
	protected $categories;

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('b.count', 'a.name', 'a.message', 'a.created');
		}

		parent::__construct($config);

		$this->modems		= $this->getModems();
		$this->categories	= $this->getCategories();
	}
	
	protected function populateState($ordering = 'a.created', $direction = 'desc') {
		parent::populateState($ordering, $direction);

		// Adjust the context to support modal layouts.
		$layout = $this->input->get('layout', 'default');
		if ($layout) {
			$this->context.= '.' . $layout;
		}
		
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$published = $this->getUserStateFromRequest('com_gtsms.conversations.default.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$modem = $this->getUserStateFromRequest($this->context . '.filter.modem', 'filter_modem', '');
		$this->setState('filter.modem', $modem);

		$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
		$this->setState('filter.category', $category);

		$type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
		$this->setState('filter.type', $type);
	}
	
	protected function getListQuery() {
		$msisdn_id = $this->input->get('id');
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Select message
		$query->select($db->quoteName(array('a.id', 'a.msisdn_id', 'a.message', 'a.modem', 'a.category_id', 'a.created')));
		$query->from($db->quoteName('#__gtsms_messages', 'a'));

		// Join Read
		$query->select('IF('.
			$db->quoteName('a.type').' = '.$db->quote('received').', (IF('.
				$db->quoteName('b.type').' IS NULL OR'.$db->quoteName('a.created').' >= '.$db->quoteName('b.modified').
			', '.$db->quote('new').', '.$db->quoteName('b.type').')'.
		'),'.$db->quoteName('a.type').') type');
		$query->join('LEFT', $db->quoteName('#__gtsms_reads', 'b') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.msisdn_id') .
			' AND ' . $db->quoteName('b.user_id') . ' = ' . $db->quote($this->user->id)
		);

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			// If contains spaces, the words will be used as keywords.
			if (preg_match('/\s/', $search)) {
				$search = str_replace(' ', '%', $search);
			}
			$search = $db->quote('%' . $search . '%');
			
			$search_query = array();
			$search_query[] = $db->quoteName('a.message') . 'LIKE ' . $search;
			$query->where('(' . implode(' OR ', $search_query) . ')');
		}

		if(!GTHelperAccess::isAdmin()) {
			$modems		= array_map(array($db, 'quote'), array_keys($this->getModems()));
			$modems		= $modems ? implode(',', $modems) : $db->quote('-99');
			$categories	= array_map(array($db, 'quote'), array_keys($this->getCategories()));
			$categories	= $categories ? implode(',', $categories) : $db->quote('-99');

			$query->where($db->quoteName('a.modem'). ' IN ('.$modems.')');
			$query->where($db->quoteName('a.category_id'). ' IN ('.$categories.')');
		}

		// Publish filter
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where($db->quoteName('a.published'). ' = ' . (int)$published);
		} else {
			$query->where($db->quoteName('a.published'). ' IN (0, 1)');
		}

		// Modem filter
		$modem = $this->getState('filter.modem');
		if ($modem) {
			$query->where($db->quoteName('a.modem'). ' = ' . $db->quote($modem));
		}

		// Messsage group filter
		$category = $this->getState('filter.category');
		if ($category) {
			$query->where($db->quoteName('a.category_id'). ' = ' . $db->quote($category));
		}

		// Type filter
		$type = $this->getState('filter.type');
		if ($type) {
			switch($type) {
				case 'new':
					$query->where('('.$db->quoteName('b.type').' IS NULL OR '.$db->quoteName('a.created').' >= '.$db->quoteName('b.modified').')');
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				case 'unread':
					$query->where('('.$db->quoteName('b.type').' IS NULL OR '.$db->quoteName('b.type').' = '.$db->quote('unread').' OR '.$db->quoteName('a.created').' >= '.$db->quoteName('b.modified').')');
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				case 'read':
					$query->where($db->quoteName('b.type').' = '.$db->quote('read'));
					$query->where($db->quoteName('a.created').' < '.$db->quoteName('b.modified'));
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				default:
					$query->where($db->quoteName('a.type'). ' = ' . $db->quote($type));
					break;
			}
		}
		
		$query->where($db->quoteName('a.msisdn_id').' = '.$msisdn_id);

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		switch ($orderCol) {
			case 'a.id':
				$query->order($db->quoteName('a.id') . ' ' . $orderDirn);
				break;
			case 'a.date' :
				$query->order('IF(DAY('.$db->quoteName('a.modified').'), '.$db->quoteName('a.modified').', '.$db->quoteName('a.created').') ' . $orderDirn);
				$query->order($db->quoteName('a.id') . ' ' . $orderDirn);
				break;
			default:
				$query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
				$query->order($db->quoteName('a.id') . ' ' . $orderDirn);
				break;
		}
		
		//echo nl2br(str_replace('#__','eburo_',$query));
		return $query;
	}

	public function getMSISDN() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('CONCAT_WS(":",`a`.`msisdn`,`a`.`msisdn_nat`,`a`.`msisdn_int`,`a`.`calling_code`,`a`.`area_code`) msisdn');
		$query->select($db->quoteName('a.type'));
		$query->select($db->quoteName('a.msisdn', 'msisdn_raw'));
		$query->from($db->quoteName('#__gtsms_msisdns', 'a'));

		// Join contact
		$query->select($db->quoteName('b.name', 'contact_name'));
		$query->join('LEFT', $db->quoteName('#__gtsms_contacts', 'b') . ' ON FIND_IN_SET(' . $db->quoteName('a.id') . ', ' . $db->quoteName('b.msisdn_ids') . ')');

		$query->where($db->quoteName('a.id').' = '.$this->input->get('id'));

		$db->setQuery($query);

		$msisdn = $db->loadObject();
		$msisdn->msisdn = GTHelperNumber::setMSISDN($msisdn->msisdn);

		return $msisdn;
	}

	public function getAvailableModems() {
		return $this->getModems(false);
	}

	public function getModemOptions() {
		return $this->modems;
	}

	public function getModems($all=true) {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.name', 'a.description')));
		$query->from($db->quoteName('#__gtsms_modems', 'a'));

		// Join MSISDN
		$query->select('CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`) msisdn');
		$query->select($db->quoteName(array('b.carrier')));
		$query->join('LEFT', $db->quoteName('#__gtsms_msisdns', 'b') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.id'));

		if(!$all) {
			// Join message
			$query->join('INNER', $db->quoteName('#__gtsms_messages', 'c') . ' ON ' . $db->quoteName('a.name') . ' = ' . $db->quoteName('c.modem'));
			$query->where($db->quoteName('c.msisdn_id').' = '.$this->input->get('id'));
		}

		if(!GTHelperAccess::isAdmin()) {
			$query->join('LEFT', $db->quoteName('#__gtsms_categories', 'd') . ' ON FIND_IN_SET('.$db->quoteName('d.id').', '.$db->quoteName('a.category_ids') . ')');
			$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('d.user_ids').')');
		}

		$query->where($db->quoteName('a.published').' = 1');

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		$modems = $db->loadObjectList('name');
		foreach ($modems as &$modem) {
			$msisdn = $modem->msisdn ? GTHelperNumber::setMSISDN($modem->msisdn) : '';
			$msisdn = implode(' - ', array_filter(array($msisdn, $modem->carrier)));
			$msisdn = $msisdn ? $modem->name.' ('.$msisdn.')' : $modem->name;

			$modem->msisdn = $msisdn;
		}
		return $modems;
	}

	public function getCategoryOptions() {
		return $this->categories;
	}

	public function getCategories($all=true) {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));

		if(!$all) {
			// Join message
			$query->join('INNER', $db->quoteName('#__gtsms_messages', 'c') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('c.category_id'));
			$query->where($db->quoteName('c.msisdn_id').' = '.$this->input->get('id'));
		}

		if(!GTHelperAccess::isAdmin()) {
			$query->where('FIND_IN_SET('.$this->user->id.', '.$db->quoteName('a.user_ids').')');
		}

		$query->where($db->quoteName('a.published').' = 1');

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);

		$categories = $db->loadObjectList('id');

		return $categories;
	}

	public function getItems($is_table = false) {
		$items		= parent::getItems($is_table);
		$modems		= $this->getModems();
		$categories	= $this->getCategories();
		$types		= array(
			'process'	=> array('arrow-up', 'orange', JText::_('COM_GTSMS_OPT_TYPE_PROCESS')),
			'sent'		=> array('check', 'green', JText::_('COM_GTSMS_OPT_TYPE_SENT')),
			'failed'	=> array('ban', 'red', JText::_('COM_GTSMS_OPT_TYPE_FAILED')),
			'new'		=> array('asterisk', 'purple', JText::_('COM_GTSMS_OPT_TYPE_NEW')),
			'unread'	=> array('comment', 'blue', JText::_('COM_GTSMS_OPT_TYPE_UNREAD')),
			'read'		=> array('comment-o', 'cyan', JText::_('COM_GTSMS_OPT_TYPE_READ'))
		);
		
		$messages = array();
		$idlen = 0;
		foreach ($items as $i => $item) {
			$type		= $types[$item->type];
			$typeIcon	= '<span class="btn btn-'.$type[1].' btn-sm hasTooltip" title="'.$type[2].'"><i class="fa fa-'.$type[0].'"></i></span>';
			
			$modem		= @$modems[$item->modem];
			$category	= @$categories[$item->category_id];
			$status		= array();
			$status[]	= @$category->name ? '<span class="fa fa-inbox"></span> '.$category->name : '';
			$status[]	= @$modem->msisdn ? '<span class="fa fa-signal"></span> '.$modem->msisdn : '';
			$status		= implode(' | ', array_filter($status));

			$msgClass	= 'bubble ';
			$msgClass	.= in_array($item->type, array('new', 'unread', 'read')) ? 'bubble-left ' : 'bubble-right pull-right ';
			$msgClass	.= $item->type;

			$msg	= '<div class="'.$msgClass.'">';
			$msg	.= '<div><div class="icon">'.$typeIcon.'</div><div class="msg">';
			$msg	.= nl2br(trim($item->message));
			$msg	.= '<hr/><small>'.$status.'</small></div></div></div>';
			
			$diff	= GTHelperDate::diff($item->created);
			$unix	= strtotime($item->created);
			$date	= JHtml::date($item->created, 'd M Y H:i');

			$message				= new stdClass();
			$message->id			= JHtml::_('grid.id', $i, $item->id);
			$message->message		= $msg;
			$message->date			= $date.'<br/><small>'.$diff.'</small>';
			$message->modem			= $item->modem;

			$idlen = $idlen ? $idlen : strlen($item->id)+2;
			$messages[$unix.str_pad($item->id, $idlen, '0', STR_PAD_LEFT)] = $message;
		}
		ksort($messages);
		return array_values($messages);
	}

	public function getUpdateRead() {
		$this->updateRead($this->input->get('id'));
	}

	public function updateRead($msisdn_ids, $type = 'read') {		
		$msisdn_ids = is_array($msisdn_ids) ? $msisdn_ids : array($msisdn_ids);
		$msisdn_ids = array_filter($msisdn_ids);
		$read_ids	= GTHelper::getReferences($msisdn_ids, 'reads', 'msisdn_id', 'id', null, 'msisdn_id');
		foreach ($msisdn_ids as $msisdn_id) {
			$read 				= new stdClass();
			$read->id			= intval(@$read_ids[$msisdn_id]);
			$read->msisdn_id	= $msisdn_id;
			$read->user_id		= $this->user->id;
			$read->type			= $type;

			$readTb = $this->getTable('Read');
			$readTb->bind(JArrayHelper::fromObject($read));
			$readTb->store();
		}

		return true;
	}

	public function updateUnread($msisdn_ids) {
		return $this->updateRead($msisdn_ids, 'unread');
	}

	public function changeCategory() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$pks = $this->input->get('cid', array(), 'array');
		$pks = array_map(array($db, 'quote'), $pks);
		$category_id = $this->input->get('change_category_id');

		// Fields
		$fields		= array();
		$fields[]	= $db->quoteName('a.category_id') . ' = ' . $db->quote($category_id);
		
		// Conditions
		$conditions		= array();
		$conditions[]	= $db->quoteName('a.id') . ' IN ('. implode(',', $pks) . ')';
		
		$query->update($db->quoteName('#__gtsms_messages', 'a'))->set($fields)->where($conditions);
		 
		$db->setQuery($query);

		//echo nl2br(str_replace('#__','eburo_',$query)); die;

		return $db->execute();
	}
}
