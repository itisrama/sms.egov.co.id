<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('text');

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Provides a hidden field
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.hidden.html#input.hidden
 * @since       11.1
 */
class JFormFieldAltCalendar extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'AltCalendar';

	protected function getInput() {
		// Load JSs
		$format		= (string) $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';

		if(is_numeric(strpos($format, '%'))) {
			$format = str_replace(array('%', 'd', 'm', 'Y'), array('', 'dd', 'mm', 'yyyy'), $format);
		}
		
		$deflang	= explode('-', JComponentHelper::getParams('com_languages')->get('site'));
		$deflang	= reset($deflang);
		$minView	= isset($this->element['minView']) ? $this->element['minView'] : '0';
		$maxView	= isset($this->element['maxView']) ? $this->element['maxView'] : '2';
		$document	= JFactory::getDocument();
		$document->addScript(GT_ADMIN_JS . '/datepicker/bootstrap-datepicker.js');
		$document->addScript(GT_ADMIN_JS . '/datepicker/locales/bootstrap-datepicker.' . $deflang . '.js');
		$document->addStylesheet(GT_ADMIN_CSS . '/bootstrap-datepicker3.min.css');
		$document->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					$('#". $this->id ."_container').datepicker({ format: '". $format ."', language: '". $deflang ."', minViewMode: '". $minView ."', maxViewMode: '". $maxView ."'})
				});
			})(jQuery);
		");

		$app =& JFactory::getApplication();
		if ($app->isSite()) {
			$input = '<div class="input-group date '.$this->class.'" id="'.$this->id.'_container" >';
			$input .= '<span class="input-group-addon btn btn-danger" onclick="jQuery(this).next().val(null)"><i class="fa fa-times"></i></span>';
			$input .= preg_replace('/class=".*?"/', 'class="form-control" readonly="" style="background:none"', parent::getInput());
			$input .= '<span class="input-group-addon btn iconcal btn-info"><i class="fa fa-calendar"></i></span></div>';
		} else {
			$input = '<div class="input-prepend input-append date '.$this->class.'" id="'.$this->id.'_container" >';
			$input .= '<span class="btn" onclick="jQuery(this).next().val(null)"><i class="icon-remove"></i></span>';
			$input .= preg_replace('/class=".*?"/', 'readonly="" style="background:none"', parent::getInput());
			$input .= '<span class="btn iconcal btn-info"><i class="icon-calendar"></i></span></div>';
		}
		
		

		return $input;		
	} 
}
