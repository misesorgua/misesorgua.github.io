<?php
/**
 *  RPX Component Controller
 * 
 * @website http://www.ultijoomla.com
 * @license	GNU/GPL
 */


jimport('joomla.application.component.controller');
if (!defined('SERVICES_JSON_SLICE')) {
  require_once(JPATH_BASE.'/components/com_rpx/JSON.php');
}
class RPXController extends JController
{
	function display()
	{
		parent::display();
	}

}
?>
