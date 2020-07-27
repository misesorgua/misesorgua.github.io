<?php
/**
 * Entry point file for RPX Component
 * 
 * @website http://www.ultijoomla.com
 * @license	GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Create the controller
$classname	= 'RPXController'.$controller;
$controller = new $classname();


$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

?>
