<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script file of the jQuery Easy plugin
 */
class plgsystemjqueryeasyInstallerScript
{	
	static $version = '1.6.3';
				
	/**
	 * Called before an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent) {
		//echo '<br />';
	}
	
	/**
	 * Called after an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent) 
	{			
		echo '<p style="margin: 20px 0">';
		//echo '<img src="../plugins/system/jqueryeasy/images/logo.png" />';
		echo JText::_('PLG_SYSTEM_JQUERYEASY_VERSION_LABEL').' <span class="label">'.self::$version.'</span>';
		echo '<br /><br />Olivier Buisard @ <a href="http://www.simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';
		
		if ($type == 'update') {
			
			echo '<dl>';
			echo '    <dt>Change log</dt>';
			echo '    <dd><span class="label label-inverse">MODIFIED</span> jQuery UI theme set to <em>none</em> as default</dd>';
			echo '    <dd><span class="label label-inverse">MODIFIED</span> getting the Migrate script from the jQuery CDN</dd>';
			echo '    <dd><span class="label label-warning">REMOVED</span> version number from language files</dd>';
			echo '    <dd><span class="label label-info">UPDATED</span> links to articles from the help tab</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> links to support and bug reporting</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> help translate link</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> online documentation link</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> Portuguese (Brazil locale)</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> support for Migrate v1.3.0</dd>';
			echo '    <dd><span class="label label-success">ADDED</span> author, version and translators custom fields</dd>';
			echo '</dl>';
		}
		
		return true;
	}
	
	/**
	 * Called on installation
	 *
	 * @return  boolean  True on success
	 */
	public function install($parent) {}
	
	/**
	 * Called on update
	 *
	 * @return  boolean  True on success
	 */
	public function update($parent) {}
	
	/**
	 * Called on uninstallation
	 */
	public function uninstall($parent) {}
	
}
?>
