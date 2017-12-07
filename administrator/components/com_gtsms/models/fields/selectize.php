<?php
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldSelectize extends JFormFieldList
{
	
	protected $type = 'Selectize';
	
	protected function getOptions() {
		$this->value = is_object($this->value) ? JArrayHelper::fromObject($this->value) : $this->value;
		$this->value = is_numeric($this->value) ? array($this->value) : $this->value;
		$this->value = $this->value ? $this->value : array(0);

		$db	= JFactory::getDBO();
		$id	= $this->id;

		$task		= (string) $this->element['task'];
		$requests	= (string) $this->element['requests'];
		$requests	= $requests ? $requests : '{}';
		$create		= isset($this->element['create']) ? $this->element['create'] : 'false';
		$preload	= isset($this->element['preload']) ? $this->element['preload'] : 'true';

		$reqs			= json_decode(GTHelper::fixJSON($requests));
		$reqs			= is_object($reqs) ? $reqs : new stdClass();
		$reqs->task		= $task;
		$reqs->ids		= $this->value;
		$reqs->user_id	= JFactory::getUser()->id;
		$url		= GT_GLOBAL_COMPONENT . '&' . http_build_query($reqs);
		$items		= $preload == 'true' || array_sum($this->value) ? (array) json_decode(file_get_contents($url)) : array();
		
		$options = array();
		foreach ($items as $item) {
			$options[] = JHtml::_('select.option', $item->id, $item->label);
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
						persist: true,
						valueField: 'id',
						labelField: 'label',
						searchField: 'name',
						sortField: 'name',
						create: $create,
						preload: $preload,
						load: function(query, callback) {
							data = $requests;
							data.task = '$task';
							data.search = query;
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

		return $options;
	}


}
?>
