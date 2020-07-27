<?php
/**
* @version		1.0 RokComments
* @package		RokComments
* @copyright	Copyright (C) 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see RT-LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JElementMenus extends JElement {

	var	$_name = 'menus';
	
	function fetchElement($name, $value, &$node, $control_name){
		$document =& JFactory::getDocument();
		$menus = array();
		$temp->value = '';
		$temp->text = JText::_("Select All");
		$menus = JHTML::_('menu.linkoptions');
		array_unshift($menus,$temp);
		$output = JHTML::_('select.genericlist',  $menus, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:95%;" multiple="multiple" size="10"', 'value', 'text', $value );
		return $output;
	}

} 