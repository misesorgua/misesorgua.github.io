<?php
/**
* @version		1.0 RokComments
* @package		RokComments
* @copyright	Copyright (C) 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see RT-LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent( 'onPrepareContent', 'plgContentRokComments' );

/**
* Page break plugin
*
* <b>Usage:</b>
* <code>{rokcomments}</code>
*
*/
function plgContentRokComments( &$row, &$params, $page=0 )
{
	global $option;
	
	// return is not active
    if(JPluginHelper::isEnabled('content','rokcomments')==false || $row->id == null) return;
	
	// only works for content
	if ($option != "com_content") return;
	
	// Get Plugin info
	$user 		    =& JFactory::getUser();
	$plgname        = "rokcomments";
	$plugin			=& JPluginHelper::getPlugin('content', $plgname);
	$document       =& JFactory::getDocument();
	
	$pluginParams	= new JParameter( $plugin->params );
	$sectionid      = $row->sectionid;
	$catid          = $row->catid;
	$option 		= JRequest::getCmd('option');
	$view 			= JRequest::getCmd('view');
	$itemid 		= JRequest::getInt('Itemid');
	$regex          = '#{rokcomments}#s';
	
	$system         = $pluginParams->get('system', 'intensedebate');
    $method         = $pluginParams->get('method','id');
    $catids         = $pluginParams->get('categories','');
    $menuids        = $pluginParams->get('menus','');
    $tagmode        = $pluginParams->get('tagmode',0);
    $showcount      = $pluginParams->get('showcount',1);
    $account        = $pluginParams->get('id-account');
    $domain         = $pluginParams->get('js-domain');
    $subdomain      = $pluginParams->get('d-subdomain');
    $devmode        = $pluginParams->get('d-devmode',0);
    $commenticon    = " ".$pluginParams->get('showicon','rk-icon');

    $commentpage    = false;

    
    //add some css
    $document->addStyleSheet(JURI::base()."plugins/content/rokcomments/rokcomments.css");
    
    //get devmode for Disqus
    $devcode = "";
    if ($devmode) $devcode = "var disqus_developer = \"1\";";
	
	//url
	$baseurl = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
	if ($_SERVER['SERVER_PORT']!="80") $baseurl .= ":".$_SERVER['SERVER_PORT'];
	if ($row->access <= $user->get('aid', 0)){
		$path = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
	} else {
		$path = JRoute::_("index.php?option=com_user&view=login");
	}
	
	$url = $baseurl.$path;
	
	//commentpage
	if ($view=='article') $commentpage = true;

    // handle tag style
    if ($tagmode == 1) $postid = $row->slug;
    else $postid = $row->alias;
    
    //sepcial case for ID
    if ($system == "intensedebate") {
        $postid = str_replace(array("-",":"),array("_","_"),$postid);
    }
    
    
    // get array of category ids
    if (is_array($catids)){
		$categories = $catids;
	} elseif ($catids==''){
		$categories[] = $catid;
	} else {
		$categories[] = $catids;
	}
    
    // get array of menus ids
    if (is_array($menuids)){
		$menus = $menuids;
	} elseif ($menuids==''){
		$menus[] = $itemid;
	} else {
		$menus[] = $menuids;
	}
    

    // check to make sure we are where we should be
    if ($method == 'code') {
    	if (!(strpos( $row->text, '{rokcomments}' ) !== false && $option=='com_content')) {
    		$row->text = preg_replace( $regex, '', $row->text );
    		return;
    	}
    } else {
        // remove rokcomments code if in there
        $row->text = preg_replace( $regex, '', $row->text );
        if( !(in_array($catid,$categories) && in_array($itemid,$menus) && $option=='com_content')) {
            return;
        }
    }
    
    // check to make sure commentcount should be shown
    if (!$commentpage and $showcount==0) return;

    	
	if ( $system == 'disqus') {
        // disqus
        if ($commentpage == false) {
            $output = "<div class=\"rk-commentcount{rk-icon}\"><a class=\"rokcomment-counter\" href=\"{post-url}#disqus_thread\" title=\"Comments\">Comments</a></div>\n";
            if (!defined('ROKCOMMENT_COUNT')) {

                $headscript = "
                <script type='text/javascript'>
				window.addEvent('domready', function(){
					// Disqus Counter
					var links = document.getElementsByTagName('a');
					var query = '?';
					for(var i = 0; i < links.length; i++) {
						if(links[i].href.indexOf('#disqus_thread') >= 0) query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
					}
					var disqusScript = document.createElement('script');
					disqusScript.setAttribute('charset','utf-8');
					disqusScript.setAttribute('type','text/javascript');
					disqusScript.setAttribute('src','http://disqus.com/forums/{subdomain}/get_num_replies.js' + query + '');
					var b = document.getElementsByTagName('body')[0];
					b.appendChild(disqusScript);
                });
                </script>";
                $headscript = str_replace("{subdomain}", $subdomain, $headscript);
                $document->addCustomTag($headscript);
                define('ROKCOMMENT_COUNT',true);
            }
        } else {
            $output = '
            <div id="disqus_thread"></div>
			<script type="text/javascript">
				//<![CDATA[
				{devcode}
				var disqus_url= "{post-url}";
				var disqus_identifier = "{post-id}";
				//]]>
			</script>            
            <script type="text/javascript" src="http://disqus.com/forums/{subdomain}/embed.js"></script><noscript><a href="http://{subdomain}.disqus.com/?url=ref">View the discussion thread.</a></noscript><a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>';
        }

    } elseif ( $system == 'jskit') {
        // js-kit
        if ($commentpage == false) {
            if (!defined('ROKCOMMENT_COUNT')) {
                $headscript = "
                <script type='text/javascript'>
				window.addEvent('domready', function(){
					var jskitScript = document.createElement('script');
					jskitScript.setAttribute('charset','utf-8');
					jskitScript.setAttribute('type','text/javascript');
					jskitScript.setAttribute('src','http://js-kit.com/for/{domain}/comments-count.js');
					var b = document.getElementsByTagName('body')[0];
					b.appendChild(jskitScript);
                });
                </script>";
                $headscript = str_replace('{domain}', $domain, $headscript);
                $document->addCustomTag($headscript);
                define('ROKCOMMENT_COUNT',1);
            }
            $output = '<div class="rk-commentcount{rk-icon}"><a href="{post-path}">Comments (<span class="js-kit-comments-count" uniq="{post-path}">0</span>)</a></div>';
        } else {
            $output = '<div style="margin-top:25px;" class="js-kit-comments" permalink="{post-url}" path=""></div><script src="http://js-kit.com/for/{domain}/comments.js"></script>';
        }

    } else {
        // intense debate
        
        if ($commentpage == false) {
    	    $output = '
    		<script type="text/javascript">
    		var idcomments_acct = "{account}";var idcomments_post_id = "{post-id}";var idcomments_post_url = "{post-url}";
    		</script>
    		<div class="rk-commentcount{rk-icon}">    		
    		<script type="text/javascript" src="http://www.intensedebate.com/js/genericLinkWrapperV2.js"></script>
    		</div>';      
        } else {
    	    $output = '
    		<script type="text/javascript">
    		var idcomments_acct = "{account}";var idcomments_post_id = "{post-id}";var idcomments_post_url = "{post-url}";
    		</script>
    		<span id="IDCommentsPostTitle" style="display:none"></span>
    		<script type="text/javascript" src="http://www.intensedebate.com/js/genericCommentWrapperV2.js"></script>';
        }
        
        


    }
    $search     = array('{subdomain}','{post-id}','{post-url}','{post-path}','{devcode}','{rk-icon}','{domain}','{account}');
    $replace    = array($subdomain,$postid,$url,$path,$devcode,$commenticon,$domain,$account);
    
    $output = str_replace($search, $replace , $output);


	if ($method == 'code') {
	    $row->text = preg_replace( $regex, $output, $row->text );
    } else {
        $row->text .= $output;
    }

	return true;
}