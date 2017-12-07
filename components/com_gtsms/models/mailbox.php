<?php

/**
 * @package     GT Component
 * @author      Yudhistira Ramadhan
 * @link        http://gt.web.id
 * @license     GNU/GPL
 * @copyright   Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSModelMailbox extends GTModelList
{
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('b.count', 'a.contact', 'a.message', 'a.date');
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'a.created', $direction = 'desc') {
		parent::populateState($ordering, $direction);

		// Adjust the context to support modal layouts.
		$layout = $this->input->get('layout', 'default');
		if ($layout) {
			$this->context.= '.' . $layout;
		}
		
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '1');
		$this->setState('filter.published', $published);
		
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$search_by = $this->getUserStateFromRequest($this->context . '.filter.search_by', 'filter_search_by', 'message');
		$this->setState('filter.search_by', $search_by);

		$modem = $this->getUserStateFromRequest($this->context . '.filter.modem', 'filter_modem', '');
		$this->setState('filter.modem', $modem);

		$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
		$this->setState('filter.category', $category);

		$type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
		$this->setState('filter.type', $type);
	}
	
	protected function getListQuery() {
		$lastvisit = $this->user->lastvisitDate;

		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Select message
		$query->select($db->quoteName(array('a.id', 'a.msisdn_id', 'a.message', 'a.modem', 'a.category_id', 'a.published')));
		$query->select('IF(DAY('.$db->quoteName('a.modified').'), '.$db->quoteName('a.modified').', '.$db->quoteName('a.created').') date');
		$query->from($db->quoteName('#__gtsms_messages', 'a'));

		// Join contact
		$query->select($db->quoteName('b.name', 'contact'));
		$query->join('LEFT', $db->quoteName('#__gtsms_contacts', 'b') . ' ON 
			FIND_IN_SET(' . $db->quoteName('a.msisdn_id') . ', ' . $db->quoteName('b.msisdn_ids') . ')'
		);

		// Join MSISDN
		$query->select('CONCAT_WS(":",`c`.`msisdn`,`c`.`msisdn_nat`,`c`.`msisdn_int`,`c`.`calling_code`,`c`.`area_code`) msisdn');
		$query->join('INNER', $db->quoteName('#__gtsms_msisdns', 'c') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('c.id'));

		// Join Read
		$query->select('IF('.
			$db->quoteName('a.type').' = '.$db->quote('received').', (IF('.
				$db->quoteName('d.type').' IS NULL OR'.$db->quoteName('a.created').' >= '.$db->quoteName('d.modified').
			', '.$db->quote('new').', '.$db->quoteName('d.type').')'.
		'),'.$db->quoteName('a.type').') type');
		$query->join('LEFT', $db->quoteName('#__gtsms_reads', 'd') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('d.msisdn_id') .
			' AND ' . $db->quoteName('d.user_id') . ' = ' . $db->quote($this->user->id)
		);

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
					$query->where('('.$db->quoteName('d.type').' IS NULL OR '.$db->quoteName('a.created').' >= '.$db->quoteName('d.modified').')');
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				case 'unread':
					$query->where('('.$db->quoteName('d.type').' IS NULL OR '.$db->quoteName('d.type').' = '.$db->quote('unread').' OR '.$db->quoteName('a.created').' >= '.$db->quoteName('d.modified').')');
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				case 'read':
					$query->where($db->quoteName('d.type').' = '.$db->quote('read'));
					$query->where($db->quoteName('a.created').' < '.$db->quoteName('d.modified'));
					$query->where($db->quoteName('a.type').' = '.$db->quote('received'));
					break;
				default:
					$query->where($db->quoteName('a.type'). ' = ' . $db->quote($type));
					break;
			}
		}
		
		$search = $this->getState('filter.search');
		$search_by = $this->getState('filter.search_by');
		if (!empty($search)) {
			// If contains spaces, the words will be used as keywords.
			if (preg_match('/\s/', $search)) {
				$search = str_replace(' ', '%', $search);
			}
			$search = $db->quote('%' . $search . '%');
			
			$search_query = array();
			switch ($search_by) {
				case 'contact':
					$search_query[] = $db->quoteName('b.name') . 'LIKE ' . $search;
					$search_query[]	= 'CONCAT_WS(":",`c`.`msisdn`,`c`.`msisdn_nat`,`c`.`msisdn_int`) LIKE ' . $search;
					break;
				case 'message':
				default:
					$search_query[] = $db->quoteName('a.message') . 'LIKE ' . $search;
					break;
			}
			
			$query->where('(' . implode(' OR ', $search_query) . ')');
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->group($db->quoteName('a.id'));

		switch ($orderCol) {
			case 'a.contact':
				$query->order('IF('.$db->quoteName('b.name'). ' IS NULL, 1, 0) ' . $orderDirn);
				$query->order('IF('.$db->quoteName('b.name'). ' IS NULL, '.$db->quoteName('c.msisdn') . ', '. $db->quoteName('b.name') . ') ' . $orderDirn);
				break;
			case 'a.date' :
				$query->order('IF(DAY('.$db->quoteName('a.modified').'), '.$db->quoteName('a.modified').', '.$db->quoteName('a.created').') ' . $orderDirn);
				$query->order($db->quoteName('a.id') . ' ' . $orderDirn);
				break;
			default:
				$query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
				break;
		}
		//echo nl2br(str_replace('#__','eburo_',$query));
		return $query;
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
			
			$msisdn		= GTHelperNumber::setMSISDN($item->msisdn);
			$contact	= $item->contact ? '<strong>'.$item->contact.'</strong><br/>'.$msisdn : '<strong>'.$msisdn.'</strong>';

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
			$msg	.= nl2br(trim($item->message));
			$msg	.= '<hr/><small>'.$status.'</small></div>';
			
			$diff	= GTHelperDate::diff($item->date);
			$unix	= strtotime($item->date);
			$date	= JHtml::date($item->date, 'd M Y H:i');

			$message			= new stdClass();
			$message->id		= JHtml::_('grid.id', $i, $item->id);
			$message->type		= $typeIcon;
			$message->contact	= $contact;
			$message->message	= $msg;
			$message->date 		= $date.'<br/><small>'.$diff.'</small>';

			$idlen = $idlen ? $idlen : strlen($item->id)+2;
			$messages[$unix.str_pad($item->id, $idlen, '0', STR_PAD_LEFT)] = $message;
		}
		ksort($messages);
		return array_values($messages);
	}

	public function getModems() {
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.name', 'a.description')));
		$query->from($db->quoteName('#__gtsms_modems', 'a'));

		// Join contact
		$query->select('CONCAT_WS(":",`b`.`msisdn`,`b`.`msisdn_nat`,`b`.`msisdn_int`,`b`.`calling_code`,`b`.`area_code`) msisdn');
		$query->select($db->quoteName(array('b.carrier')));
		$query->join('LEFT', $db->quoteName('#__gtsms_msisdns', 'b') . ' ON ' . $db->quoteName('a.msisdn_id') . ' = ' . $db->quoteName('b.id'));

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

	public function getCategories() {		
		// Get a db connection.
		$db = $this->_db;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));

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
}
