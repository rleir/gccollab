<?php

elgg_register_event_handler('init', 'system', 'contactform_init');

function contactform_init() {
	// Get config
	global $CONFIG;
	elgg_extend_view('css/elgg', 'pages/css');
	elgg_register_library('contact_lib', elgg_get_plugins_path().'contactform/lib/functions.php');
    elgg_load_library('contact_lib');
  	$action_path = elgg_get_plugins_path() . 'contactform/actions/contactform';
	elgg_register_action('contactform/delete', "$action_path/delete.php");
    requirements_check2();
	elgg_register_page_handler('contactform','contactform_page_handler');



	// cyu - register action to send the feedback form to the helpdesk/and send copy to recipient using elgg function
	elgg_register_action('contactform/send_feedback', "$action_path/send_feedback.php",'public');

}

function contactform_page_handler($page) {
	global $CONFIG;
	switch ($page[0])
	{
		default:
			include $CONFIG->pluginspath . 'contactform/index.php';
			break;
	}
	exit;
}

