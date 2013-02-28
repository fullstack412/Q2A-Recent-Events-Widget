<?php
/*
	Plugin Name: Recent Events Widget
	Plugin Description: Displays the newest events of your q2a forum in a widget
	Plugin Version: 0.2
	Plugin Date: 2013-02-27
	Plugin Author: echteinfachtv
	Plugin Author URI: http://www.echteinfach.tv/
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.5

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
	
*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
}

// widget
qa_register_plugin_module('widget', 'qa-recent-events-widget.php', 'qa_recent_events_widget', 'Recent Events Widget');

// language file
qa_register_plugin_phrases('qa-recent-events-widget-lang.php', 'qa_recent_events_widget_lang');




// custom function to get all events and new events
function getAllForumEvents($queryRecentEvents, $eventsToShow, $region) {

	$maxEventsToShow = 5;
	$listAllEvents = '';
	$countEvents = 0;

	while ( ($row = qa_db_read_one_assoc($queryRecentEvents,true)) !== null ) {
		if(in_array($row['event'], $eventsToShow)) {
		
			// question title
			$qTitle = '';
			
			// workaround: convert tab jumps to & to be able to use query function
			$toURL = str_replace("\t","&",$row['params']);
			// echo $toURL."<br />"; // we get e.g. parentid=4523&parent=array(65)&postid=4524&answer=array(40)
			parse_str($toURL, $data);  // parse URL to associative array $data
			// now we can access the following variables in array $data if they exist in toURL
			
			$linkToPost = "-";
			
			// find out type, if Q set link directly, if A or C do query to get correct link
			$postid = (isset($data['postid'])) ? $data['postid'] : null;
			if($postid !== null) {
				$getPostType = mysql_fetch_array( qa_db_query_sub("SELECT type,parentid FROM `^posts` WHERE `postid` = #", $postid) );
				$postType = $getPostType[0]; // type, and $getPostType[1] is parentid
				if($postType=="A") {
					$getQtitle = mysql_fetch_array( qa_db_query_sub("SELECT title FROM `^posts` WHERE `postid` = # LIMIT 1", $getPostType[1]) );
					$qTitle = (isset($getQtitle[0])) ? $getQtitle[0] : "";
					// get correct public URL
					$activity_url = qa_path_html(qa_q_request($getPostType[1], $qTitle), null, qa_opt('site_url'), null, null);
					$linkToPost = $activity_url."?show=".$postid."#a".$postid;
				}
				else if($postType=="C") {
					// get question link from answer
					$getQlink = mysql_fetch_array( qa_db_query_sub("SELECT parentid,type FROM `^posts` WHERE `postid` = # LIMIT 1", $getPostType[1]) );
					$linkToQuestion = $getQlink[0];
					if($getQlink[1]=="A") {
						$getQtitle = mysql_fetch_array( qa_db_query_sub("SELECT title FROM `^posts` WHERE `postid` = # LIMIT 1", $getQlink[0]) );
						$qTitle = (isset($getQtitle[0])) ? $getQtitle[0] : "";
						// get correct public URL
						$activity_url = qa_path_html(qa_q_request($linkToQuestion, $qTitle), null, qa_opt('site_url'), null, null);
						$linkToPost = $activity_url."?show=".$postid."#c".$postid;
					}
					else {
						// default: comment on question
						$getQtitle = mysql_fetch_array( qa_db_query_sub("SELECT title FROM `^posts` WHERE `postid` = # LIMIT 1", $getPostType[1]) );
						$qTitle = (isset($getQtitle[0])) ? $getQtitle[0] : "";
						// get correct public URL
						$activity_url = qa_path_html(qa_q_request($getPostType[1], $qTitle), null, qa_opt('site_url'), null, null);
						$linkToPost = $activity_url."?show=".$postid."#c".$postid;
					}
				}
				// if question is hidden, do not show frontend!
				else if($postType=="Q_HIDDEN") {
					$qTitle = '';
				}
				else {
					// question has correct postid to link
					// $questionTitle = (isset($data['title'])) ? $data['title'] : "";
					$getQtitle = mysql_fetch_array( qa_db_query_sub("SELECT title FROM `^posts` WHERE `postid` = # LIMIT 1", $postid) );
					$qTitle = (isset($getQtitle[0])) ? $getQtitle[0] : "";
					// get correct public URL
					// $activity_url = qa_path_html(qa_q_request($getPostType[1], $qTitle), null, qa_opt('site_url'), null, null);
					$activity_url = qa_path_html(qa_q_request($postid, $qTitle), null, qa_opt('site_url'), null, null);
					$linkToPost = $activity_url;
				}
			}
			
			$username = (is_null($row['handle'])) ? qa_lang_html('qa_recent_events_widget_lang/anonymous') : htmlspecialchars($row['handle']);
			$usernameLink = (is_null($row['handle'])) ? qa_lang_html('qa_recent_events_widget_lang/anonymous') : '<a target="_blank" class="qa-user-link" style="font-weight:normal;" href="'.qa_opt('site_url').'user/'.$row['handle'].'">'.htmlspecialchars($row['handle']).'</a>';
			
			// set event name and css class
			$eventName = '';
			$eventNameShort = '';
			if($row['event']=="q_post") {
				$eventName = qa_lang_html('qa_recent_events_widget_lang/new_question');
				$eventNameShort = qa_lang_html('qa_recent_events_widget_lang/new_question_abbr');
			}
			else if($row['event']=="a_post") {
				$eventName = qa_lang_html('qa_recent_events_widget_lang/new_answer');
				$eventNameShort = qa_lang_html('qa_recent_events_widget_lang/new_answer_abbr');
			}
			else if($row['event']=="c_post") {
				$eventName = qa_lang_html('qa_recent_events_widget_lang/new_comment');
				$eventNameShort = qa_lang_html('qa_recent_events_widget_lang/new_comment_abbr');
			}
			else if($row['event']=="a_select") {
				$eventName = qa_lang_html('qa_recent_events_widget_lang/new_bestanswer');
				$eventNameShort = qa_lang_html('qa_recent_events_widget_lang/new_bestanswer_abbr');
			}
			
			// display date as 'before x time'
			// $timeCode = implode('', qa_when_to_html( strtotime($row['datetime']), qa_opt('show_full_date_days')));
			
			// if question title is empty, question got possibly deleted, do not show frontend!
			if($qTitle=='') {
				continue;
			}
			
			// widget output, e.g. <a href="#" title="Antwort von echteinfachtv">17:23h A: Terme lösen und auskl...</a>
			$evTime = substr($row['datetime'],11,5) . qa_lang_html('qa_recent_events_widget_lang/hour_indic'); // 17:23h
			$qTitleShort = substr($qTitle,0,22); // shorten question title to 22 chars
			$qTitleShort2 = (strlen($qTitle)>80) ? htmlspecialchars( substr($qTitle,0,60) ).'&hellip;' : htmlspecialchars($qTitle); // shorten question title to 60 chars
			
			if ($region=='side') {
				$listAllEvents .= '<a class="tipsify" href="'.$linkToPost.'" title="'.$eventName.' '.qa_lang_html('qa_recent_events_widget_lang/new_by').' '.$username.': '.htmlspecialchars($qTitle).'">'.$evTime.' '.$eventNameShort.': '.htmlspecialchars($qTitleShort).'&hellip;</a>';
			}
			else {
				$listAllEvents .= '<a href="'.$linkToPost.'">'.$evTime.' '.$eventName.' '.qa_lang_html('qa_recent_events_widget_lang/new_by').' '.$username.': '.$qTitleShort2.'</a>';
			}
			$countEvents++;
			if($countEvents>=$maxEventsToShow) {
				break;
			}
		}
	}
	// header('Content-Type: text/plain; charset=utf-8');
	return utf8_encode($listAllEvents);
} // end function getAllForumEvents()
		
		
/*
	Omit PHP closing tag to help avoid accidental output
*/