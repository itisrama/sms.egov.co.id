<?php

/**
 * @package		GT Component
 * @author		Yudhistira Ramadhan
 * @link		http://gt.web.id
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2012 GtWeb Gamatechno. All Rights Reserved.
 */

defined('_JEXEC') or die;

class GTSMSViewImport_Regency extends GTView {

	var $data;
	var $form;
	var $item;
	var $state;
	var $layout;
	
	public function display($tpl = null) {
		// Get model data.
		$this->state		= $this->get('State');
		$this->params		= $this->state->params;
		
		$this->layout		= $this->getLayout();
		$this->form			= $this->get('Form');
		$this->isNew		= true;
		
		// Load Script
		$this->document->addScriptDeclaration('var global_hlimit = 3');
		$this->document->addScript(JK_GLOBAL_JS . '/statistics.js');

		// Assign additional data
		$this->canDo = GTHelperAccess::getActions();
		
		// Check permission and display
		GTHelperAccess::checkPermission($this->canDo);

		if($this->layout == 'preview') {
			$this->data				= JArrayHelper::toObject(JRequest::getVar('jform', null, 'array', 'array'));

			$items					= $this->readFile();
			$this->items 			= array();
			$this->json 			= array();
			foreach ($items as $markets) {
				foreach ($markets as $market_id => $commodities) {
					$this->json[$market_id] = $commodities;
					foreach ($commodities as $commodity_id => $price) {
						$this->items[$commodity_id][$market_id] = $price;
					}
				}
			}
			$this->json 			= htmlentities(json_encode($this->json));
			$this->city				= $this->get('City');
			$this->markets			= $this->get('Markets');
			$this->commodityList	= $this->get('CommodityList');
		}
		$this->addToolbar();
		GTHelper::addSubmenu('import_regency');
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function readFile() {
		//Retrieve file details from uploaded file, sent from upload form
		$file			= JKHelper::arrayToFiles(JRequest::getVar('jform', null, 'files', 'array'));
		$format			= $this->get('Format');
		
		// Read your Excel workbook
		JLoader::import('phpexcel.Classes.PHPExcel');
		JLoader::import('phpexcel.Classes.PHPExcel.IOFactory');
		try {
			$inputFileType	= PHPExcel_IOFactory::identify($file->file_excel->tmp_name);
			$objReader		= PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel	= $objReader->load($file->file_excel->tmp_name);
		} catch(Exception $e) {
		    die('Error loading file "'.pathinfo($filename,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		
		$data		= array();
		$mcolumns	= json_decode($format->market_columns);
		foreach ($mcolumns as $index => $markets) {
			$sheet = $objPHPExcel->getSheet($index);
			$data[$index] = $this->readWorksheet($sheet, $markets, $format);
		}

		return $data;
	}

	protected function readWorksheet($sheet, $markets, $format) {
		/*$rs		= $format->start_row;
		$re		= $format->end_row;*/
		$rs		= 1;
		$re		= $sheet->getHighestRow();
		$ccoms	= explode(',', $format->commodity_column);

		// Get worksheet dimensions
		$commodities_db	= $this->get('CommodityNames');

		$comdb_names = array();
		foreach ($commodities_db as $k => $commodity) {
			$comdb_names[$k] = JKHelper::cleanstr($commodity->original_name);
		}

		//echo "<pre>"; print_r($comdb_names); echo "</pre>";

		$coms 			= array();
		foreach ($ccoms as $k => $ccom) {
			$coms[$k]	= array_map('current', $sheet->rangeToArray($ccom.$rs.':'.$ccom.$re, NULL, TRUE, FALSE));
		}
		
		$commodities 	= array();
		for($i = $rs-$rs; $i<=$re-$rs; $i++) {
			foreach ($ccoms as $k => $ccom) {
				$commodity = strlen(trim($coms[$k][$i])) > 0 ? trim($coms[$k][$i]) : @$commodities[$i];
				$commodities[$i] = JKHelper::cleanstr($commodity);
			}
		}

		$commodity_ids	= array();
		//echo "<pre>"; print_r($commodities); echo "</pre>";
		foreach ($commodities as $row => $commodity) {
			$commodity_id = array_search($commodity, $comdb_names);
			if(!$commodity_id) continue;
			
			unset($comdb_names[$commodity_id]);
			$commodity_ids[$row] = $commodity_id;
		}
		//echo "<pre>"; print_r($commodity_ids); echo "</pre>";

		$data	= array();
		foreach ($markets as $market_id => $cmkt) {
			$prices	= array_map('current', $sheet->rangeToArray($cmkt.$rs.':'.$cmkt.$re, NULL, TRUE, FALSE));
			foreach ($commodity_ids as $row => $commodity_id) {
				$commodity_db = $commodities_db[$commodity_id];
				if(!intval($prices[$row])>0) continue;
				$data[$market_id][$commodity_id] = $prices[$row] * $commodity_db->multiplier;
			}
		}

		return $data;
	}
	
	protected function addToolbar()	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);

		JToolbarHelper::title(sprintf(JText::_('COM_GTSMS_TITLE_IMPORT_EXCEL'), JText::_('COM_GTSMS_TITLE_REGENCY')), 'list menus');

		// If not checked out, can save the item.
		if ($this->layout == 'preview')
		{
			JToolbarHelper::custom('import_regency.send', 'icon-envelope', 'icon-arrow-right', JText::_('COM_GTSMS_BTN_SEND_EXCEL'));
			JToolbarHelper::back();
		} else {
			JToolbarHelper::custom('', 'icon-upload', 'icon-upload', JText::_('COM_GTSMS_BTN_UPLOAD'));
		}
	}

}
