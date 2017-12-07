<?php
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldMsCategory extends JFormFieldList
{
	protected $type = 'MsCategory';
	
	protected function getOptions() {
		// Get Feeder ID
		$input		= JFactory::getApplication()->input;

		// DB Objects
		$db	= JFactory::getDBO();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('a.id', 'a.name')));
		$query->from($db->quoteName('#__gtsms_categories', 'a'));
		$query->where($db->quoteName('a.published').' = 1');

		if(!GTHelperAccess::isAdmin()) {
			$user = JFactory::getUser();
			$query->where('FIND_IN_SET('.$user->id.', '.$db->quoteName('a.user_ids').')');
		}

		$query->group($db->quoteName('a.id'));
		$query->order($db->quoteName('a.name'));

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','eburo_',$query));

		$categories = $db->loadObjectList();
		$options	= array();
		foreach ($categories as $category) {
			$options[] = JHtml::_('select.option', $category->id, $category->name);
		}
		
		// Merge any additional options in the XML definition.
		$options	= array_merge(parent::getOptions(), $options);

		return $options;
	}
}
?>
