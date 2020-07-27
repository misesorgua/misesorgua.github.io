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

jimport( 'joomla.plugin.plugin' );

class plgContentJw_disqus extends JPlugin {

	function plgContentJw_disqus( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$row, &$params, $limitstart ){

    // JoomlaWorks reference parameters
    $plg_name               = "jw_disqus";
    $plg_copyrights_start   = "\n\n<!-- JoomlaWorks \"Disqus Comment System for Joomla!\" Plugin (v2.2) starts here -->\n";
    $plg_copyrights_end     = "\n<!-- JoomlaWorks \"Disqus Comment System for Joomla!\" Plugin (v2.2) ends here -->\n\n";

		// API
    $mainframe	= &JFactory::getApplication();
		$document 	= &JFactory::getDocument();
		$db 				= &JFactory::getDBO();
		$user 			= &JFactory::getUser();
		$aid 				= $user->get('aid',0);
		
		// Assign paths
    $sitePath = JPATH_SITE;
    $siteUrl  = substr(JURI::root(), 0, -1);
    
		// Requests
		$option 		= JRequest::getCmd('option');
		$view 			= JRequest::getCmd('view');
		$layout 		= JRequest::getCmd('layout');
		$page 			= JRequest::getCmd('page');
		$secid 			= JRequest::getInt('secid');
		$catid 			= JRequest::getInt('catid');
		$itemid 		= JRequest::getInt('Itemid');
		if(!$itemid) $itemid = 999999;
        
    // Check if plugin is enabled
    if(JPluginHelper::isEnabled('content',$plg_name)==false) return;
    
		// Simple checks before parsing the plugin
		$properties = get_object_vars($row);
		if (!(array_key_exists('catid',$properties) && array_key_exists('sectionid',$properties))) return;
		if(!$row->id || $option=='com_rokdownloads') return;
		
		
		    
		// ----------------------------------- Get plugin parameters -----------------------------------
		$plugin =& JPluginHelper::getPlugin('content', $plg_name);
		$pluginParams = new JParameter( $plugin->params );

		$selectedCategories			= $pluginParams->get('selectedCategories','');
		$selectedMenus					= $pluginParams->get('selectedMenus','');
		$disqusSubDomain				= trim($pluginParams->get('disqusSubDomain',0));
		$disqusListingCounter		= $pluginParams->get('disqusListingCounter',1);
		$disqusArticleCounter		= $pluginParams->get('disqusArticleCounter',1);
		$disqusDevMode					= $pluginParams->get('disqusDevMode',0);
		$debugMode							= $pluginParams->get('debugMode',0);
		if($debugMode==0) error_reporting(0); // Turn off all error reporting

		// Quick check before we proceed
		if(!$disqusSubDomain){
			global $raiseDisqusNotice;
			if(!$raiseDisqusNotice){
				$raiseDisqusNotice=1;
				JError::raiseNotice('',JText::_("Please enter your Disqus subdomain in order to use the Disqus Comment System! If you don't have a Disqus.com account <a target=\"_blank\" href=\"http://disqus.com/comments/register/\">register for one here</a>"));
			}
			return;
		}
		
		// Perform some cleanups
		if($disqusSubDomain) $disqusSubDomain = str_replace(array('http://','.disqus.com/','.disqus.com'), array('','',''), $disqusSubDomain);
				
		// External parameter for controlling plugin layout within modules
		if(!$params) $params = new JParameter(null);
		$parsedInModule = $params->get('parsedInModule');
		
		
		
		// ----------------------------------- Before plugin render -----------------------------------

		// Get the current category
		if(is_null($row->catslug)){
			$currectCategory = 0;
		} else {
			$currectCategory = explode(":",$row->catslug);
			$currectCategory = $currectCategory[0];	
		}

		// Define plugin category restrictions
		if (is_array($selectedCategories)){
			$categories = $selectedCategories;
		} elseif ($selectedCategories==''){
			$categories[] = $currectCategory;
		} else {
			$categories[] = $selectedCategories;
		}
		
		// Define plugin menu restrictions
		if (is_array($selectedMenus)){
			$menus = $selectedMenus;
		} elseif (is_string($selectedMenus) && $selectedMenus!=''){
			$menus[] = $selectedMenus;
		} elseif ($selectedMenus==''){
			$menus[] = $itemid;
		}


		
		// ----------------------------------- Prepare elements -----------------------------------
		
		// Includes
		require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		require_once(dirname(__FILE__).DS.$plg_name.DS.'includes'.DS.'helper.php');
		
		// Output object
		$output = new JObject;

		// Article URLs (raw, browser, system)
		$itemURLraw = $siteUrl.'/index.php?option=com_content&view=article&id='.$row->id;
		
		$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
		$itemURLbrowser = $websiteURL.$_SERVER['REQUEST_URI'];
		$itemURLbrowser = explode("#",$itemURLbrowser);
		$itemURLbrowser = $itemURLbrowser[0];
		
		if ($row->access <= $user->get('aid', 0)){
			$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
		} else {
			$itemURL = JRoute::_("index.php?option=com_user&task=register");
		}
		
		// Article URL assignments
		$output->itemURL 					= $websiteURL.$itemURL;
		$output->itemURLrelative 	= $itemURL;
		$output->itemURLbrowser		= $itemURLbrowser;
		$output->itemURLraw				= $itemURLraw;

		// Fetch elements specific to the "article" view only
		if( in_array($currectCategory,$categories) && in_array($itemid,$menus) && $option=='com_content' && $view=='article'){
		
			// Comments (article page)
			$output->comments = '
			<div id="disqus_thread"></div>
			<script type="text/javascript">
				//<![CDATA[
			';
			if($disqusSubDomain=='disqusforjoomla' || $disqusDevMode){
				$output->comments .= '
					var disqus_developer = "1";
				';
			}
			$output->comments .= '
					var disqus_url= "'.$output->itemURL.'";
					var disqus_identifier = "'.substr(md5($disqusSubDomain),0,10).'_id'.$row->id.'";
				//]]>
			</script>
			<script type="text/javascript" src="http://disqus.com/forums/'.$disqusSubDomain.'/embed.js"></script>
			<noscript>
				<a href="http://'.$disqusSubDomain.'.disqus.com/?url=ref">'.JText::_("View the discussion thread.").'</a>
			</noscript>
			<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
			';

		} // End fetch elements specific to the "article" view only



		// ----------------------------------- Head tag includes -----------------------------------
		
		$dsqCSS = JWDisqusHelper::getTemplatePath($plg_name,'css/disqus.css');
		$dsqCSS = $dsqCSS->http;
		
		$plgCSS = JWDisqusHelper::getTemplatePath($plg_name,'css/template.css');
		$plgCSS = $plgCSS->http;
		
		$output->includes = "
		{$plg_copyrights_start}
		<script type=\"text/javascript\" src=\"{$siteUrl}/plugins/content/{$plg_name}/includes/js/behaviour.js\"></script>
		<script type=\"text/javascript\">
			//<![CDATA[
			var disqusSubDomain = '{$disqusSubDomain}';
			var disqus_iframe_css = \"{$dsqCSS}\";
			//]]>
		</script>
		<style type=\"text/css\" media=\"all\">
			@import \"{$plgCSS}\";
		</style>
		{$plg_copyrights_end}
		";
		
		
		
		// ----------------------------------- Render the output -----------------------------------		
		if( in_array($currectCategory,$categories) && in_array($itemid,$menus) ){
		
				// Load the plugin language file the proper way
				if($mainframe->isAdmin()){
					JPlugin::loadLanguage( 'plg_content_'.$plg_name );
				} else {
					JPlugin::loadLanguage( 'plg_content_'.$plg_name, 'administrator' );
				}

				// Output head includes
				JHTML::_('behavior.mootools');
				JWDisqusHelper::loadHeadIncludes($output->includes);
									
				if( ($option=='com_content' && $view=='article') && $parsedInModule!=1){

					// Fetch the template
					ob_start();
					$dsqArticlePath = JWDisqusHelper::getTemplatePath($plg_name,'article.php');
					$dsqArticlePath = $dsqArticlePath->file;
					include($dsqArticlePath);
					$getArticleTemplate = $plg_copyrights_start.ob_get_contents().$plg_copyrights_end;
					ob_end_clean();
	
					// Output
					$row->text = $getArticleTemplate;
					
				} else if( $disqusListingCounter && (($option=='com_content' && ($view=='frontpage' || $view=='section' || $view=='category')) || $parsedInModule==1) ){
				
					// Fetch the template
					ob_start();
					$dsqListingPath = JWDisqusHelper::getTemplatePath($plg_name,'listing.php');
					$dsqListingPath = $dsqListingPath->file;
					include($dsqListingPath);
					$getListingTemplate = $plg_copyrights_start.ob_get_contents().$plg_copyrights_end;
					ob_end_clean();
						
					// Output
					$row->text = $getListingTemplate;
									
				}
				
		} // END IF
		  
	} // END FUNCTION

} // END CLASS
