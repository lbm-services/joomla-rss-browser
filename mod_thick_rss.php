<?php
/**
* @file mod_thick_rss.php  $Format:%ci$
* @package mod_thick_rss
* @author Horst Lindlbauer info@lbm-services.de
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @ $version: 3.4$
* @description: Joomla module to show list of RSS feeds and link to news in modal window
**/
// no direct access

DEFINE('DS', DIRECTORY_SEPARATOR);

defined('_JEXEC') or die('Restricted access');
// Include the helper functions only once
require_once (dirname(__FILE__).DS.'helper.php');


// load Headerscripts

$instance= modThickRSSHelper::setHeader($params);

// Get data from helper class
$rss_content = modThickRSSHelper::getFeed($params);
// Run default template script for output
require(JModuleHelper::getLayoutPath('mod_thick_rss'));


