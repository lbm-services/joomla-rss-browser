<?php
/**
 * @file helper.php  $Format:%ci$
 * @package mod_thick_rss
 * @author Horst Lindlbauer info@lbm-services.de
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @ $version: 3.4.2$
 * @credit: Boris Popoff (smoothbox), Ryan Parman, Geoffrey Sneddon (SimplePie), Codey Lindley (Thickbox), David Thomas (Slick RSS)
 * @description: Joomla module to show list of RSS feeds and link to news in modal window
 **/


// following line is to prevent direct access to this script via the url
defined('_JEXEC') or die('Restricted access');

jimport('simplepie.simplepie');

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
		$desc_limit			= $params->get( 'desc_limit', 0 );
		$rssimage 			= $params->get( 'rssimage', 1 );//?
		$newsWinY			= $params->get( 'newsWinY', 650);
		$newsWinX			= $params->get( 'newsWinX', 850);
		$rssitemdesc			= $params->get( 'rssitemdesc', 1 );
		$showdate			= $params->get( 'showdate', 0 );
		$dateformat			= $params->get( 'dateformat', '' );
		$dateposition		= $params->get( 'dateposition', 0 );
		$words_t			= $params->def( 'word_count_title', 0 );
		$words_d			= $params->def( 'word_count_descr', 0 );
		$rsstitle			= $params->get( 'rsstitle', 1 );
		$rsscache			= $params->get( 'rsscache', 60 );
		//Custom
		$enable_tooltip 		= $params->get( 'enable_tooltip','no' );
		$moduleclass_sfx 		= $params->get( 'moduleclass_sfx', '' );
		$disable_modal     		= $params->get( 'disable_modal', 0 );
		$disable_errors    		= $params->get( 'disable_errors', 0 );


		$content_buffer	= '';
		$rssurls = preg_split("/[\s]+/", $rssurl);

		$content_buffer	.= "<!-- RSS BROWSER, http://www.lbm-services.de  START -->\n";
		$content_buffer	.=  '<div class="rss-container'.$moduleclass_sfx.'">';
		
		foreach($rssurls as $rssurl){

			if( trim($rssurl) != "") { 			// only if array element is not empty

				if (!empty($rssurl)) {

				$feed = new SimplePie();
				$feed->set_feed_url($rssurl);
				//echo $rssurl. "<br/>"; //debug
				
				//set / override Joomla HTML page metatag charset to match RSS character encoding
				$feed->handle_content_type(); 
				$feed->set_cache_location($cacheDir);
				$feed->set_cache_duration( $rsscache*60 );
				

				if (!$feed->init())
					{
                    if ( 0 == $disable_errors){
                        JFactory::getApplication()->enqueueMessage( "Could not load feed: $rssurl", 'error');
                    } 
                JLog::addLogger(
                   array(
                        'text_file' => 'mod_thick_rss.connection_errors.php',
                        'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'
                   ),
                   JLog::ALL & ~JLog::DEBUG,
                   array('mod_thick_rss')
               );
                        JLog::add("Could not load feed: $rssurl", JLog::ERROR, 'mod_thick_rss' );
						continue;
					}
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
						if ($disable_modal == 0 ) $content_buffer .= '<a href="'. JFilterOutput::ampReplace($iLink . $cChr.'keepThis=true&TB_iframe=true&height='. $newsWinY .'&width='. $newsWinX) .'" title="'.$feed_title .'" class="lightbox"><img style="display:inline;" src="'. $iUrl .'" alt="'. $iTitle .'" border="0"/></a>';
                         			else $content_buffer .= '<a href="javascript://" title="'.$iTitle .'"  onclick="window.open(\''.$ilink.'\',\'news\',\'toolbar=no,location=no,scrollbars=1,status=no,menubar=no,width=' .$newsWinX.',height=' .$newsWinY.', top=50,left=150\').focus();"><img style="display:inline;" src="'. $iUrl .'" alt="'. $iTitle .'" border="0"/></a>';
						
					}
					if (strpos($feed_link,"?") === false ) $cChr = "?";
					else $cChr = "&";

					if ($rsstitle) {
						$content_buffer .= "<h4 class=\"rssfeed_title".$moduleclass_sfx."\">";
						if ($disable_modal == 0 ) $content_buffer .= '<a href="'. JFilterOutput::ampReplace($feed_link . $cChr.'keepThis=true&TB_iframe=true&height='. $newsWinY .'&width='. $newsWinX) .'" title="'.$feed_title .'" class="lightbox">';
						else $content_buffer .= '<a href="javascript://" title="'.$feed_title .'" onclick="window.open(\''.$feed_link.'\',\'news\',\'toolbar=no,location=no,scrollbars=1,status=no,menubar=no,width=' .$newsWinX.',height=' .$newsWinY.', top=50,left=150\').focus();">';
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



					$content_buffer .= "		<ul class=\"rssfeed_list" . $moduleclass_sfx . "\">\n";
					for ($j = 0; $j < $totalItems; $j++) {

						$currItem =& $feed->get_item($j);
						$item_title = $currItem->get_title();
						$date = $currItem->get_date();
						// "d.m.Y H:i"
						if ($dateformat != '') $date = date($dateformat , strtotime($date));
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
						if ($disable_modal == 0) $content_buffer .= '<a href="' . $currItem->get_permalink(). '" title="'. $tooltip. '" class="lightbox" data-group="news" data-height="'.$newsWinY .'" data-width="'.$newsWinX.'"  >';
							else $content_buffer .= '<a href="javascript://" title="'. $tooltip. '" onclick="window.open(\''.$currItem->get_permalink().'\',\'news\',\'toolbar=no,location=no,scrollbars=1,status=no,menubar=no,width=' .$newsWinX.',height=' .$newsWinY.', top=50,left=150\').focus();">';
						if ($showdate == 1 ){
							$dateposition == 0 ? $content_buffer .= $item_title . "<span class=\"rssfeed_date" . $moduleclass_sfx . "\">".$date . "</span> " : $content_buffer .= "<span class=\"rssfeed_date" . $moduleclass_sfx . "\"> ".$date . "</span> " . $item_title ;
						} else {
							$content_buffer .= $item_title ;
						}
						$content_buffer .= "</a>\n";
						if ($rssdesc_2 > 0) {
							$desc = strip_tags($currItem->get_description());
							if ($desc_limit > 0  && strlen($desc) > $desc_limit  ) $desc = substr($desc, 0, $desc_limit )  ."..." ;
							$content_buffer .= "<br/>".$desc ."<br/>";
						}
							
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


	function setHeader(&$params) {

		$disable_modal = $params->get( 'disable_modal', 0 );
		$style = $params->get( 'design', 'default' );
		static $instance;
		$document =& JFactory::getDocument();
		$live_site= JURI::base(true) ;
		$mod_path = $live_site . "/modules/mod_thick_rss";


		if (!isset($instance)){

			$html = '';
			$html .="<link rel=\"stylesheet\" href=\"". $mod_path. "/includes/styles/modthickrss.css\" type=\"text/css\" media=\"screen\" />".PHP_EOL;
		if ($disable_modal == 0) {
			
			$html .= '<script type="text/javascript">
			var basepath = "'. $live_site .'";
			</script>'.PHP_EOL;
			$html .= '<script type="application/json" id="easyOptions">
			{"news": {"overlayOpacity": 0.1}}
			</script>'.PHP_EOL;
			$html .="<script type=\"text/javascript\" src=\"" . $mod_path . "/includes/distrib.min.js\"></script>".PHP_EOL;
			$html .="<link rel=\"stylesheet\" href=\"". $mod_path. "/includes/styles/$style/easybox.min.css\" type=\"text/css\" media=\"screen\" />".PHP_EOL;
			}
		$document->addCustomTag( $html );
		$instance = true;
		}

	}

}
