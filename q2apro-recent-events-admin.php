<?php
/*
	Plugin Name: Remind Users after Registration
	Plugin URI: http://www.q2apro.com/plugins/remind-users
*/
	class q2apro_recent_events{
		function init_queries($tableslc) {
			return null;
		}
		// option's value is requested but the option has not yet been set
		function option_default($option) {
			switch($option) {
				case 'q2apro_recent_events_counts':
					return 5; 
				case 'q2apro_recent_events_time_format':
					return '0'; 
				default:
					return null;
			}
		}
			
		function allow_template($template) {
			return ($template!='admin');
		}       
			
		function admin_form(&$qa_content){                       
			// process the admin form if admin hit Save-Changes-button
			$ok = null;
			if (qa_clicked('q2apro_recent_events_save')) {
				qa_opt('q2apro_recent_events_counts', (int)qa_post_text('q2apro_recent_events_counts'));
				qa_opt('q2apro_recent_events_time_format', qa_post_text('q2apro_recent_events_time_format'));
				$ok = qa_lang('admin/options_saved');
			}
			
			// form fields to display frontend for admin
			$fields = array();
			
			$fields[] = array(
				'type' => 'number',
				'label' => qa_lang_html('qa_recent_events_widget_lang/admin_count'),
				'tags' => 'name="q2apro_recent_events_counts"',
				'value' => qa_opt('q2apro_recent_events_counts'),
			);

			$time_format_options = array('absolute', 'relative');
			$fields[] = array(
				'type' => 'select',
				'label' => qa_lang_html('qa_recent_events_widget_lang/admin_time_format'),
				'tags' => 'name="q2apro_recent_events_time_format"',
				'value' => $time_format_options[qa_opt('q2apro_recent_events_time_format')],
				'options' => $time_format_options,
			);
			
			return array(     
				'ok' => ($ok && !isset($error)) ? $ok : null,
				'fields' => $fields,
				'buttons' => array(
					array(
						'label' => 'save',
						'tags' => 'name="q2apro_recent_events_save"',
					),
				),
			);
		}
	}
