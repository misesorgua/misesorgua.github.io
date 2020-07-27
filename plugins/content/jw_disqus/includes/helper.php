<?php 
/*
// JoomlaWorks "Disqus Comment System" Plugin for Joomla! 1.5.x - Version 2.2
// Copyright (c) 2006 - 2009 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// ***Last update: November 14th, 2009***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JWDisqusHelper {

	// Load Includes
	function loadHeadIncludes($headIncludes){
		global $loadDisqusPluginIncludes;
		$document = & JFactory::getDocument();
		if(!$loadDisqusPluginIncludes){
			$loadDisqusPluginIncludes=1;
			$document->addCustomTag($headIncludes);
		}
	}
		
	// Path overrides
	function getTemplatePath($pluginName,$file){
		global $mainframe;
		$p = new JObject;
		if(file_exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))){
			$p->file = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::base()."templates/".$mainframe->getTemplate()."/html/{$pluginName}/{$file}";
		} else {
			$p->file = JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::base()."plugins/content/{$pluginName}/tmpl/{$file}";
		}
		return $p;
	}

} // end class
