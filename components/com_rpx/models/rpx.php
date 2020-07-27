<?php
/**
 * RPX Component
 * 
 * @website http://www.ultijoomla.com
 * @license	GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class RPXModelRPX extends JModel
{
   function getKey() {
     $db =& JFactory::getDBO();
     $query = "SELECT * FROM #__rpx WHERE propname='key'";
     $db->setQuery($query);
     $row = $db->loadObject();
     return $row->propvalue;
  }
}
