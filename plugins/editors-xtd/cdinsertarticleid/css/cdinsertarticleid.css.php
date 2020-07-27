<?php
/**
 * Core Design Insert Article ID button plugin for Joomla! 1.5
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	Content
 * @category   	Plugin
 * @version		1.0.0
 * @copyright	Copyright (C)  2007 - 2008 Core Design, http://www.greatjoomla.com
 * @license     http://creativecommons.org/licenses/by-nc/3.0/legalcode Creative Commons
 */

if (extension_loaded('zlib') && !ini_get('zlib.output_compression'))
@ob_start('ob_gzhandler');
header('Content-type: text/css; charset: UTF-8');
header('Cache-Control: must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

define('DS', DIRECTORY_SEPARATOR);
$dir = dirname(__FILE__);
$filename = $dir . DS . 'cdinsertarticleid.css';

include($filename);

?>