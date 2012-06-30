<?php
/**
* @version tmpl/default.php  $Format:%ci$
* @package mod_thick_rss
* @author Horst Lindlbauer info@lbm-services.de
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @version 
* @credit: Boris Popoff (smoothbox), Ryan Parman, Geoffrey Sneddon (SimplePie), Codey Lindley (Thickbox), David Thomas (Slick RSS)
* @description: Joomla module to show list of RSS-Feeds, based on Slick RSS but using simplepie parser and Thickbox to display news.
**/
// security check - no direct access
defined('_JEXEC') or die('Restricted access');

if($params->get('enable_tooltip') == 'yes'){
        JHTML::_('behavior.tooltip','.smoothbox'); 
        $tooltips = true;
}



print $rss_content;




