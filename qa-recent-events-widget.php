<?php
/*
	Question2Answer Plugin: Recent Events Widget
	Author: http://www.echteinfach.tv/
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_recent_events_widget {
	
	function allow_template($template)
	{
		$allow=false;
		
		switch ($template)
		{
			case 'activity':
			case 'qa':
			case 'questions':
			case 'hot':
			case 'ask':
			case 'categories':
			case 'question':
			case 'tag':
			case 'tags':
			case 'unanswered':
			case 'user':
			case 'users':
			case 'search':
			case 'admin':
			case 'custom':
				$allow=true;
				break;
		}
		
		return $allow;
	}
	
	function allow_region($region)
	{
		return ($region=='side') || ($region=='main');
	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		$themeobject->output('<div class="liveBox"><p class="liveBox-link">'.qa_lang_html('qa_recent_events_widget_lang/recent_events').':</p>');
		
		// do only show the following events
		$eventsToShow = array('q_post', 'a_post', 'c_post', 'a_select');
		
		// query last 3 events
		$queryRecentEvents = qa_db_query_sub("SELECT datetime,ipaddress,handle,event,params 
									FROM `^eventlog`
									WHERE `event`='q_post' OR `event`='a_post' OR `event`='c_post' OR `event`='a_select'
									ORDER BY datetime DESC
									LIMIT 20
									"); // check with getAllForumEvents() which returns events as links

		$recentEvents = '';
		$recentEvents = getAllForumEvents($queryRecentEvents, $eventsToShow, $region);
		$themeobject->output('<div class="liveBox-events">' . $recentEvents . '</div>
			</div> <!-- end liveBox -->');
		// add fancy tooltip if widget is in sidebar
		if($region=='side') {
			$themeobject->output('<script type="text/javascript" src="https://raw.github.com/jaz303/tipsy/master/src/javascripts/jquery.tipsy.js"></script>');
			$themeobject->output('<script type="text/javascript">
				$(document).ready(function(){ 
					$(".liveBox-events a.tipsify").tipsy( {gravity: "e", fade: true, offset:5 });
				});
			</script>');
		}
		// credit developer by just a hidden link
		$themeobject->output('<a style="display:none;" href="http://www.gute-mathe-fragen.de/">Mathe Forum Schule und Studenten</a>');
	}

} // end class

/*
	Omit PHP closing tag to help avoid accidental output
*/