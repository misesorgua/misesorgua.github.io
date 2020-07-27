<?php
/**
* @version		1.0 RokComments
* @package		RokComments
* @copyright	Copyright (C) 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see RT-LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JElementCategories extends JElement {

	var	$_name = 'categories';
	
	function fetchElement($name, $value, &$node, $control_name){
		$db = &JFactory::getDBO();
		$query = 'SELECT * FROM #__sections WHERE published=1';
		$db->setQuery( $query );
		$sections = $db->loadObjectList();
		$categories=array();
		$categories[0]->id = '';
		$categories[0]->title = JText::_("Select All");
		foreach ($sections as $section) {
			$optgroup = JHTML::_('select.optgroup',$section->title,'id','title');
			$query = 'SELECT id,title FROM #__categories WHERE published=1 AND section='.$section->id;
			$db->setQuery( $query );
			$results = $db->loadObjectList();
			array_push($categories,$optgroup);
			foreach ($results as $result) {
				array_push($categories,$result);
			}
		}
		$optgroup = JHTML::_('select.optgroup',JText::_("Uncategorized"),'id','title');
		array_push($categories,$optgroup);
		$uncategorised=array();
		$uncategorised['id'] = '0';
		$uncategorised['title'] = JText::_("Uncategorized");
		array_push($categories,$uncategorised);
		return JHTML::_('select.genericlist',  $categories, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:95%;" multiple="multiple" size="10"', 'id', 'title', $value );
	}
	
}