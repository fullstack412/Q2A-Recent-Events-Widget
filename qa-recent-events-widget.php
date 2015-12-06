<?php
/*
	Question2Answer Plugin: Recent Events Widget
	Author: http://www.q2apro.com/
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
		$themeobject->output('<div class="liveBox"><p class="liveBox-link">'.qa_lang('qa_recent_events_widget_lang/recent_events').':</p>');
		
		$recentEvents = '';
		$recentEvents = getAllForumEvents($region); // string returned should be UTF8 with special chars such as äöüαβγ etc.
		// add fancy tooltip if widget is in sidebar
		if($region=='side')
		{
			$themeobject->output('
				<div class="liveboxEvents-sidebar">' . $recentEvents . '</div>
				</div> <!-- end livebox sidebar -->
			');
			$themeobject->output('
				<script type="text/javascript" src="https://raw.github.com/echteinfachtv/qa-recent-events-widget/master/tipsy.min.js"></script>
				<script type="text/javascript">
					$(document).ready(function(){ 
						$(".liveboxEvents-sidebar a").tipsy( {gravity: "e", fade: true, offset:5 });
					});
				</script>
			');
		}
		else
		{
			$themeobject->output('<div class="liveboxEvents-top">' . $recentEvents . '</div>
				</div> <!-- end livebox -->');
		}
		// css styling
		$themeobject->output('
		<style type="text/css">.liveboxEvents-sidebar
			.livebox-link {font-size:14px;color:#121212;font-weight:bold;margin-bottom:5px; }
			.liveboxEvents-sidebar { margin:10px 0 0 5px; } 
			.liveboxEvents-sidebar a, .liveboxEvents-top a { display:block; color:#253540; text-decoration:none; margin-bottom:5px; font-size:10px; }
			.tipsy{font-size:12px;position:absolute;padding:5px;z-index:100000;line-height:140%}.tipsy-inner{background-color:#000;color:#FFF;max-width:240px;padding:8px 10px 10px 10px;text-align:center}.tipsy-inner{border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px}.tipsy-arrow{position:absolute;width:0;height:0;line-height:0;border:5px dashed #000}.tipsy-arrow-n{border-bottom-color:#000}.tipsy-arrow-s{border-top-color:#000}.tipsy-arrow-e{border-left-color:#000}.tipsy-arrow-w{border-right-color:#000}.tipsy-n .tipsy-arrow{top:0;left:50%;margin-left:-5px;border-bottom-style:solid;border-top:0;border-left-color:transparent;border-right-color:transparent}.tipsy-nw .tipsy-arrow{top:0;left:10px;border-bottom-style:solid;border-top:0;border-left-color:transparent;border-right-color:transparent}.tipsy-ne .tipsy-arrow{top:0;right:10px;border-bottom-style:solid;border-top:0;border-left-color:transparent;border-right-color:transparent}.tipsy-s .tipsy-arrow{bottom:0;left:50%;margin-left:-5px;border-top-style:solid;border-bottom:0;border-left-color:transparent;border-right-color:transparent}.tipsy-sw .tipsy-arrow{bottom:0;left:10px;border-top-style:solid;border-bottom:0;border-left-color:transparent;border-right-color:transparent}.tipsy-se .tipsy-arrow{bottom:0;right:10px;border-top-style:solid;border-bottom:0;border-left-color:transparent;border-right-color:transparent}.tipsy-e .tipsy-arrow{right:0;top:50%;margin-top:-5px;border-left-style:solid;border-right:0;border-top-color:transparent;border-bottom-color:transparent}.tipsy-w .tipsy-arrow{left:0;top:50%;margin-top:-5px;border-right-style:solid;border-left:none;border-top-color:transparent;border-bottom-color:transparent}
		</style>
		');

		// credit developer by just a hidden link
		$themeobject->output('<a style="display:none;" href="http://www.mathelounge.de/">Mathe Forum Schule und Studenten</a>');
	}

} // end class

/*
	Omit PHP closing tag to help avoid accidental output
*/
