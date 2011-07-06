<?php
/**
* @file helper.php  $Format:%ci$ 
* @package mod_thick_rss
* @author $Format:%an$ $Format:%ae$
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @version 3.0
* @credit: Boris Popoff (smoothbox), Ryan Parman, Geoffrey Sneddon (SimplePie), Codey Lindley (Thickbox), David Thomas (Slick RSS)
* @description: Joomla module to show list of RSS feeds and link to news in modal window
**/


// following line is to prevent direct access to this script via the url
defined('_JEXEC') or die('Restricted access');

class modThickRSSHelper
{
	function getFeed(&$params)
	{
	
	// check if cache directory is writeable
		$cacheDir 		= JPATH_CACHE .'/';	
		if ( !is_writable( $cacheDir ) ) {	
			return 'Cache Directory Unwriteable';
		}
		
		$rssurl				= $params->get( 'rssurl' );
		$rssitems 			= $params->get( 'rssitems', 5 );
		$rssdesc 			= $params->get( 'rssdesc', 1 );
		$rssdesc_2			= $params->get( 'rssdesc_2', 0 ); // desc below link	
		$rssimage 			= $params->get( 'rssimage', 1 );//?
		$newsWinY			= $params->get( 'newsWinY', 650);
		$newsWinX			= $params->get( 'newsWinX', 850);
		$rssitemdesc			= $params->get( 'rssitemdesc', 1 );
		$words_t			= $params->def( 'word_count_title', 0 );
		$words_d			= $params->def( 'word_count_descr', 0 );
		$rsstitle			= $params->get( 'rsstitle', 1 );
		$rsscache			= $params->get( 'rsscache', 60 );
		//Custom
		$enable_tooltip     = $params->get( 'enable_tooltip','no' );
		$moduleclass_sfx 	= $params->get( 'moduleclass_sfx', '' );


		$content_buffer	= '';
		$rssurls = preg_split("/[\s]+/", $rssurl); 
		
		$content_buffer	.=  '<div class="rss-container'.$moduleclass_sfx.'" style="text-align:left;">';
		$content_buffer	.= "<!-- RSS BROWSER, http://www.lbm-services.de  START -->\n";
		foreach($rssurls as $rssurl){
			
			if( trim($rssurl) != "") { 			// only if array element is not empty

			if (!empty($rssurl)) {

			$feed = JFactory::getXMLParser( 'rss', array('rssUrl' => $rssurl ) );
				
				$feed->handle_content_type(); 
				$feed->set_cache_location($cacheDir);
				$feed->set_cache_duration( $rsscache*60 );
				$feed->init();
			}
			
				if ($feed->data) {		
				$items = $feed->get_items(); 
				$iUrl		= 0;
				$iUrl	= $feed->get_image_url();
				$iTitle	= $feed->get_image_title();
				$iLink = $feed->get_image_link();
				$feed_link = $feed->get_link() ; 
				$feed_title 	= $feed->get_title();
				$feed_descrip 	= $feed->get_description();
				if (strpos($iLink,"?") === false ) $cChr = "?";
				else $cChr = "&";
				
				if ( $rssimage && $iUrl ) {
					$content_buffer .= '<a href="'. JFilterOutput::ampReplace($iLink . $cChr.'keepThis=true&TB_iframe=true&height='. $newsWinY .'&width='. $newsWinX) .'" title="'.$feed_title .'" class="smoothbox"><img style="display:inline;" src="'. $iUrl .'" alt="'. $iTitle .'" border="0"/></a>';
				}
				if (strpos($feed_link,"?") === false ) $cChr = "?";
				else $cChr = "&";
				
				if ($rsstitle) {
					$content_buffer .= "<h4 class=\"rssfeed_title".$moduleclass_sfx."\">";
					$content_buffer .= '<a href="'. JFilterOutput::ampReplace($feed_link . $cChr.'keepThis=true&TB_iframe=true&height='. $newsWinY .'&width='. $newsWinX) .'" title="'.$feed_title .'" class="smoothbox">';
					$content_buffer .= $feed_title;
					$content_buffer .= "</a></h4>\n";
				}
				if ($rssdesc) $content_buffer .= "<div class=\"rssfeed_desc".$moduleclass_sfx."\">".$feed_descrip ."</div>\n";
				
				$actualItems 	= $feed->get_item_quantity() ;
				$setItems 		= $rssitems;
	
				if ($setItems > $actualItems) {
					$totalItems = $actualItems;
				} else {
					$totalItems = $setItems;
				}
	

				
				$content_buffer .= "		<ul class=\"rssfeed_list" . $moduleclass_sfx . "\" style=\"margin-left:0px;padding-left:0px;\" >\n";
				for ($j = 0; $j < $totalItems; $j++) {
				
					$currItem =& $feed->get_item($j);
					$item_title = $currItem->get_title();
					$text = $currItem->get_description();
					$pLink = $currItem->get_permalink();
					if (strpos($pLink, "?") === false) $cChr = "?";
					else $cChr = "&";
					
					$tooltip_title = $item_title;
					if ( $words_t ) {
					    if ( strlen($item_title) > $words_t ) {
						$item_title = substr($item_title, 0, $words_t);
						$item_title .= '...';
						}
					}
					if ( $rssitemdesc ) {
						// item description
						$text = strip_tags($currItem->get_description());
						// word limit check
						if ( $words_d ) {
							$texts = explode( ' ', $text );
							$count = count( $texts );
							if ( $count > $words_d ) {
								$text = '';
								for( $i=0; $i < $words_d; $i++ ) {
									$text .= ' '. $texts[$i];
								}
								$text .= '...';
							}
						}
					}

					if($enable_tooltip =='yes'){ 
						//generate tooltip content
						$text = preg_replace("/(\r\n|\n|\r)/", " ", $text); //replace new line characters, important!
						$text = htmlspecialchars(addslashes($text)); //format text for overlib popup html display
						$tooltip = $tooltip_title. "::".$text ;
						}else{ 
							$tooltip = "";
						}
					$content_buffer .= "<li class=\"rssfeed_item" . $moduleclass_sfx . "\">\n";
					$content_buffer .= '<a href="' . JFilterOutput::ampReplace($pLink  . $cChr.'keepThis=true&TB_iframe=true&height='. $newsWinY . '&width='. $newsWinX. '&caption='.urlencode($feed_title))  . '" title="'. $tooltip. '" class="smoothbox" rel="news">';
					$content_buffer .= $item_title; 
					$content_buffer .= "</a>\n";
					if ($rssdesc_2 > 0) $content_buffer .= "<br/>".strip_tags($currItem->get_description()) ."<br/>";
					$content_buffer .= "</li>\n";
					}
					$content_buffer .= "    </ul>\n";
					}
			}
		}
		$content_buffer .= "</div>";
		$content_buffer	.= "<!-- // RSS BROWSER   END -->\n";
			
		return $content_buffer;
	}	
}