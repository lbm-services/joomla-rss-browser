<?php
/**
* @file mod_thick_rss.php  $Format:%ci$ 
* @package mod_thick_rss
* @author $Format:%an$ $Format:%ae$
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @version 3.0 (build $version$)
* @credit: Boris Popoff (smoothbox), Ryan Parman, Geoffrey Sneddon (SimplePie), Codey Lindley (Thickbox), David Thomas (Slick RSS)
* @description: Joomla module to show list of RSS feeds and link to news in modal window
**/
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the helper functions only once
require_once (dirname(__FILE__).DS.'helper.php');


$live_site= JURI::base() ; 

$mod_path = $live_site . str_replace('\\', '/', substr( dirname(__FILE__), strlen(JPATH_BASE)+1,(strlen(__FILE__)-1)));

// vorhandene Scripte laden
$document =& JFactory::getDocument();
$scripts =& $document->_scripts ;
$loaded = false;
$html = '';

foreach($scripts as $script=>$value)
{
         if (stripos($script,"smoothbox") !== false) {
            $loaded = true;
            break;
            }
}

//print_r($scripts);
// Experimental: smoothbox nicht 2x laden, Thickbox installiert?
if (!$loaded) $html .="<script type=\"text/javascript\" src=\"" . $mod_path . "/includes/smoothbox.js\"></script>\n";
$html .="<link rel=\"stylesheet\" href=\"". $mod_path. "/includes/smoothbox.css\" type=\"text/css\" media=\"screen\" />\n";
$document->addCustomTag( $html );


// Get data from helper class
$rss_content = modThickRSSHelper::getFeed($params);
// Run default template script for output
require(JModuleHelper::getLayoutPath('mod_thick_rss'));

