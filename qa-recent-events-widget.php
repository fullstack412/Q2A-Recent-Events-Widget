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
		// add fancy tooltip if widget is in sidebar
		if($region=='side') {
			$themeobject->output('<div class="liveboxEvents-sidebar">' . $recentEvents . '</div>
				</div> <!-- end livebox sidebar -->');
			$themeobject->output('<script type="text/javascript" src="https://raw.github.com/echteinfachtv/qa-recent-events-widget/master/tipsy.min.js"></script>');
			$themeobject->output('<script type="text/javascript">
				$(document).ready(function(){ 
					$(".liveboxEvents-sidebar a").tipsy( {gravity: "e", fade: true, offset:5 });
				});
			</script>');
		}
		else {
			$themeobject->output('<div class="liveboxEvents-top">' . $recentEvents . '</div>
				</div> <!-- end livebox -->');
		}
		// css styling
		$themeobject->output('<style type="text/css">.liveboxEvents-sidebar
			.livebox-link {font-size:14px;color:#121212;font-weight:bold;margin-bottom:5px; }
			.liveboxEvents-sidebar { margin:10px 0 0 5px; } 
			.liveboxEvents-sidebar a, .liveboxEvents-top a { display:block; color:#253540; text-decoration:none; margin-bottom:5px; font-size:10px; }
		</style>');

		// credit developer by just a hidden link
		$themeobject->output('<a style="display:none;" href="http://www.gute-mathe-fragen.de/">Mathe Forum Schule und Studenten</a>');
	}

} // end class

/*
	Omit PHP closing tag to help avoid accidental output
*/