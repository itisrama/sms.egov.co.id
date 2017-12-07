<?php
/**
 * @package		GT SMS
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */
defined('_JEXEC') or die;

class GTSMSController extends GTController
{
	public function __construct($config = array())
	{
		$config['default_view'] = 'import_regency';
		parent::__construct($config);
	}
}
