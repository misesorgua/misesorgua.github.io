<?php
/**
 * Core Design Insert Article ID button plugin for Joomla! 1.5
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	Content
 * @category   	Plugin
 * @version		1.0.0
 * @copyright	Copyright (C) 2007 - 2010 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 * 
 * This file is part of Great Joomla! extension.   
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgButtonCdInsertArticleId extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param 	array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgButtonCdInsertArticleId(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
		// load plugin parameters
        //$this->plugin = &JPluginHelper::getPlugin('editors-xtd', 'cdarticleid');
        //$this->params = new JParameter($this->plugin->params);
    }

    /**
     * Display the button
     *
     * @return array A two element array of ( imageName, textToInsert )
     */
    function onDisplay($name)
    {
    	global $mainframe;
    	
		$document 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		
		JPlugin::loadLanguage('plg_editors-xtd_cdinsertarticleid', JPATH_ADMINISTRATOR); // define language   
		
		JHTML::addIncludePath(JPATH_COMPONENT . DS . 'helper');
		
		$document->addStyleSheet(JURI::root(true) . '/plugins/editors-xtd/cdinsertarticleid/css/cdinsertarticleid.css.php');
		$js = "
		function jSelectArticle(id, title, object) {
			var editor = '$name';
			jInsertEditorText(id, editor);
			document.getElementById('sbox-window').close();
		}
		";

		$document->addScriptDeclaration($js);
		
		if ($mainframe->isAdmin()) {
			$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object=id';
		} else {
			$link = JURI::root(true, 'administrator/') . 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object=id';
		}

		
		
		JHTML::_('behavior.modal');
		
        $button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('INSERT_ARTICLE_ID'));
		$button->set('name', 'cdinsertarticleid');
		$button->set('options', "{handler: 'iframe', size: {x: 650, y: 375}}");
		
        return $button;
    }
}

?>
