<?php

/**
 * Custom index widgets
 */
 
	$display_avatar = $vars['entity']->display_avatar;
	if( !isset($display_avatar) ) $display_avatar = 'yes';
	
	$widget_users = $vars['entity']->widget_users;
	$widget_title = $vars['entity']->widget_title;
	
	$guest_only = $vars['entity']->guest_only;
	if( !isset($guest_only) ) $guest_only = "no";
	
	$box_style = $vars['entity']->box_style;
	if( !isset($box_style) ) $box_style = "collapsable";
?>
<p>
	<?php echo elgg_echo('custom_index_widgets:widget_title'); ?>:
	<?php
		echo elgg_view('input/text', array(
			'name' => 'params[widget_title]',                        
			'value' => $widget_title
		));
	?>
</p>
<p>
	<?php echo elgg_echo('user'); ?>: 
	<?php
		echo elgg_view('input/text', array(
			'name' => 'params[widget_users]',                        
			'value' => $widget_users
		));
	?>
</p>
<p>
	<?php echo elgg_echo('custom_index_widgets:display_avatar'); ?>
	<?php
		echo elgg_view('input/dropdown', array(
			'name' => 'params[display_avatar]',
			'options_values' => array('yes' => 'yes', 'no' => 'no'),
			'value' => $display_avatar
		));
	?>
</p>
<p>
	<?php echo elgg_echo('custom_index_widgets:box_style'); ?>:
	<?php
		echo elgg_view('input/dropdown', array('name' => 'params[box_style]', 'options_values' => array('plain' => 'Plain', 'plain collapsable' => 'Plain and collapsable', 'collapsable' => 'Collapsable', 'standard' => 'No Collapsable'), 'value' => $box_style));
	?>
</p>
<p>
	<?php echo elgg_echo('custom_index_widgets:guest_only'); ?>:
	<?php
		echo elgg_view('input/dropdown', array('name' => 'params[guest_only]', 'options_values' => array('yes' => 'yes', 'no' => 'no'), 'value' => $guest_only));
	?>
</p>