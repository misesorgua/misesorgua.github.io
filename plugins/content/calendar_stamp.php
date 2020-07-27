<?php
/**
 * Calendar Stamp
 * @package calendar_stamp
 * @author Ahmad Alfy
 * @version 1.5
 * @copyright Non-Commercial
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @based on Calendar Icon by Sebastian Pieczynski
 **/
defined('_JEXEC') or die('Restricted access.');
jimport('joomla.event.plugin');
class plgContentCalendar_Stamp extends JPlugin
{
    function plgContentCalendar_Stamp(&$subject)
    {
        parent::__construct($subject);
        $this->_plugin = JPluginHelper::getPlugin('content', 'calendar_stamp');
        $this->_params = new JParameter($this->_plugin->params);
        global $mainframe;
        $config_style = $this->_params->get('style', 'classic_dark_blue.css');
        if ($config_style == 'user_style') {
            $config_user_style = $this->_params->get('style_user');
            $mainframe->addCustomHeadTag('<style type="text/css"><!--' . $config_user_style . '--></style>');
        } else {
            $mainframe->addCustomHeadTag('<link rel="stylesheet" href="plugins/content/calendar_stamp/' . $config_style . '" type="text/css" media="screen"/>');
        }
    }
    function _code_td_calendar(&$style, &$article_date)
    {
        $config_style = $this->_params->get('style');
       	$lang =& JFactory::getLanguage();
		$lang->load('plg_content_calendar_stamp',JPATH_ADMINISTRATOR);
		/* Creating date format according to the template */
        if ($config_style != 'user_style') {
            $fulldate    = JHTML::_('date', $article_date,JText::_('DATE_FORMAT_LC4'));
            $month_dec   = date('m', strtotime($article_date));
            $fulldate    = str_replace('  ', ' ', $fulldate);
            $explodedate = explode('.', $fulldate);
            $day         = null;
            $month       = null;
            $year        = null;
            $stamp       = null;
            /* day [01] */
            $day .= $explodedate[0];
            /* month */
            if (($style == 'classic') || ($style == 'thin') || ($style == 'simple')) {
                $month .= JText::_('SHORT_'.$explodedate[1]);
            } else {
                $month .= JText::_('FULL_'.$explodedate[1]);
            }
            /* year [2009] */
            if ($style == 'classic') {
                $year .= '20'.$explodedate[2];
            }
            /* building output */
            $stamp .= '<div class="day">' . $day . '</div>';
            $stamp .= '<div class="month">' . $month . '</div>';
            if ($style == 'classic') {
                $stamp .= '<div class="year">' . $year . '</div>';
            }
            return $stamp;
        } else if ($config_style == 'user_style') {
            /* If style_user -> load date parameters */
            $config_show_day   = $this->_params->get('show_day', '1');
            $config_show_month = $this->_params->get('show_month', '1');
            $config_show_year  = $this->_params->get('show_year', '1');
			$config_month_lenght = $this->_params->get('month_lenght', '0');
            /* getting month format
            2 formats used Numbers (01,02,03...) and Letters (January,February...)
			*/
            $month_format      = $this->_params->get('month_format', 'LETTERS');
            /* getting year format
            2 formats used YEAR_FORMAT_4 (2009) and YEAR_FORMAT_2 (09)
            */
            $year_format       = $this->_params->get('year_format', 'YEAR_FORMAT_4');
            $fulldate          = JHTML::_('date', $article_date, JText::_('DATE_FORMAT_LC4'));
            $month_dec         = date('m', strtotime($article_date));
            $fulldate          = str_replace('  ', ' ', $fulldate);
            $explodedate = explode('.', $fulldate);
            $day   = null;
            $year  = null;
            $month = null;
            $stamp = null;
			$day .= $explodedate[0];
			if ($month_format == "NUMBERS"){
				$month .= $explodedate[1];
			} else if ($month_format == "LETTERS"){
				if ($config_month_lenght == 0){
					$month .= JText::_('SHORT_'.$explodedate[1]);
				}else if ($config_month_lenght == 1){
					$month .= JText::_('FULL_'.$explodedate[1]);
				}
			}
			if ($year_format == "YEAR_FORMAT_4") {
				$year .= '20' . $explodedate[2];
			} else {
				$year .= $explodedate[2];
            }
            if ($config_show_day == 1) {
                $stamp .= '<div class="day">' . $day . '</div>';
            }
            if ($config_show_month == 1) {
                $stamp .= '<div class="month">' . $month . '</div>';
            }
            if ($config_show_year == 1) {
                $stamp .= '<div class="year">' . $year . '</div>';
            }
            return $stamp;
        }
    }
    function plgContentCalendarCheckSecCatArt(&$row)
    {
        $plugin =& JPluginHelper::getPlugin('content', 'calendar_stamp');
        $pluginParams   = new JParameter($plugin->params);
        $pluginRegistry = $pluginParams->_registry['_default']['data'];
        $value_sec      = 0;
        $value_cat      = 0;
        $value_art      = 0;
        if ($pluginRegistry->sections != '') {
            // Check accepted section	
            $aAcceptedSectionsArray = array();
            $aAcceptedSectionsArray = explode(',', $pluginRegistry->sections);
            if (in_array($row->sectionid, $aAcceptedSectionsArray) != true)
                $value_sec = '1';
            unset($aAcceptedSectionsArray);
        }
        // Check accepted category
        if ($pluginRegistry->categories != '') {
            $aAcceptedCategoryArray = array();
            $aAcceptedCategoryArray = explode(',', $pluginRegistry->categories);
            if (in_array($row->catid, $aAcceptedCategoryArray) != true) {
                $value_cat = '1';
            }
            unset($aAcceptedCategoryArray);
        }
        // Check ignored articles
        if ($pluginRegistry->articles != '') {
            $aIgnoredArticleArray = array();
            $aIgnoredArticleArray = explode(',', $pluginRegistry->articles);
            if (in_array($row->id, $aIgnoredArticleArray)) {
                $value_art = '1';
            }
            unset($aIgnoredArticleArray);
        }
        if (($value_sec == 1) || ($value_cat == 1) || ($value_art == 1)) {
            return true;
        } else {
            return false;
        }
    }
    function _draw(&$row, &$params)
    {
        $config_layout = $this->_params->get('layout', 'table');
        if ($this->plgContentCalendarCheckSecCatArt($row) == false) {
            $style_name   = $this->_params->get('style');
            $explodestyle = explode('_', $style_name);
            $style        = $explodestyle[0];
            /* If the article has not been modified yet; use the creation date
            Thanks to Agostino Zanutto for the fix
            */
            if ($this->_params->get('showing', '0') == '1' && $row->modified != '0000-00-00 00:00:00') {
                $date_used = $row->modified;
            } else {
                $date_used = $row->created;
            }
            $calendar = $this->_code_td_calendar($style, $date_used);
            /* Show/Hide original date */
            if (!$this->_params->get('original_date', '1'))
                $params->set('show_create_date', '0');
            $send = null;
            if ($config_layout == 'table') {
                $send .= '<table width="100%" border="0"><tr valign="top">';
                $send .= '<td class="stamp"><div class="datetime">';
                $send .= $calendar . '</div></td><td>';
                echo $send;
                /* building beginning of table */
                $row->text = '</td></tr></table></td></tr></table><table width="100%"><tr><td>' . $row->text;
                /* building end of table */
            } else if ($config_layout == 'css') {
                $send .= '<div class="datetime">';
                $send .= $calendar . '</div>';
                echo $send;
            }
        }
    }
    function onBeforeDisplayContent(&$row, &$params)
    {
        $config_displaying = $this->_params->get('displaying');
        if ($config_displaying == 0) {
            //frontpage only
            if (isset($row->author) && (JRequest::getVar('view') == 'frontpage'))
                $this->_draw($row, $params);
        } else if ($config_displaying == 1) {
            //frontpage + articles
            if (isset($row->author) && ((JRequest::getVar('view') == 'frontpage') || (JRequest::getVar('view') == 'article')))
                $this->_draw($row, $params);
        } else if ($config_displaying == 2) {
            //everywhere
            if (isset($row->author))
                $this->_draw($row, $params);
        } else if ($config_displaying == 3) {
            //articles only
            if (isset($row->author) && (JRequest::getVar('view') == 'article'))
                $this->_draw($row, $params);
        } else if ($config_displaying == 4) {
            //articles only + blogs
            if (isset($row->author) && ((JRequest::getVar('view') == 'article') || (JRequest::getVar('view') == 'section') || (JRequest::getVar('view') == 'category'))) {
                $this->_draw($row, $params);
            }
        }
    }
}
?>
