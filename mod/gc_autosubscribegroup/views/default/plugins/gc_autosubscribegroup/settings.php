<?php

/**
 * Elgg autosubscribegroup plugin
 * This plugin allows new users to get joined to groups automatically when they register.
 *
 * @package autosubscribegroups
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author RONNEL Jérémy
 * @copyright (c) Elbee 2008
 * @link /www.notredeco.com
 *
 * for Elgg 1.8 onwards by iionly (iionly@gmx.de)
 */

function limit_text($text, $limit = 30) {
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}

?>

<style type="text/css">
	table.group-table		{ border-right:1px solid #ccc; border-bottom:1px solid #ccc; margin: 10px 0; }
	table.group-table th 	{ background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
	table.group-table td 	{ padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
	.align-center			{ text-align: center; }
</style>

<script type="text/javascript">
	$(function() {
		var autoGroups = $("#autoGroupList").val();
		var autoGroupsArray = "";
		if(autoGroups != ""){ autoGroupsArray = autoGroups.split(','); }

		$(".auto-subscribe").change(function(e){
			var id = $(this).val();
			autoGroupsArray = jQuery.grep(autoGroupsArray, function(value) { return value != id; });
			if(this.checked){ autoGroupsArray.push(id); }
		    $("#autoGroupList").val(autoGroupsArray.join(","));
		});

		$(".admin-subscribe").change(function(e){
			$(".admin-subscribe").attr('checked', false);
			$(this).attr('checked', true);
			var id = $(this).val();
		    $("#adminGroupList").val(id);
		});
	});
</script>

<?php

$autoGroups = explode(',', $vars['entity']->autogroups);
$adminGroups = explode(',', $vars['entity']->admingroups);
$groups = elgg_get_entities(array(
	'types' => 'group',
	'limit' => 0,
));
sort($groups);

echo '<table class="group-table"><thead><tr><th>ID</th><th>' . elgg_echo('autosubscribe:name') . '</th><th>Description</th><th>' . elgg_echo('autosubscribe:autogroups') . '</th><th>' . elgg_echo('autosubscribe:admingroups') . '</th></tr></thead><tbody>';
foreach($groups as $group){
	$autoChecked = (in_array($group->guid, $autoGroups)) ? " checked": "";
	$adminChecked = (in_array($group->guid, $adminGroups)) ? " checked": "";
	if($group->enabled == "yes"){
		echo '<tr>';
		echo '<td>' . $group->guid . '</td>';
		echo '<td>' . $group->name . '</td>';
		echo '<td>' . limit_text(strip_tags($group->description)) . '</td>';
		echo '<td class="align-center"><input class="auto-subscribe" type="checkbox" value="' . $group->guid . '"' . $autoChecked . ' /></td>';
		echo '<td class="align-center"><input class="admin-subscribe" type="checkbox" value="' . $group->guid . '"' . $adminChecked . ' /></td>';
		echo '</tr>';
	}
}
echo '</tbody></table>';

echo elgg_view('input/text', array('type' => 'hidden', 'id' => 'autoGroupList', 'name' => 'params[autogroups]', 'value' => $vars['entity']->autogroups));
echo elgg_view('input/text', array('type' => 'hidden', 'id' => 'adminGroupList', 'name' => 'params[admingroups]', 'value' => $vars['entity']->admingroups));
