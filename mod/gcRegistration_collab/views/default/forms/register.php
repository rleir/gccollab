<?php
/**
 * Elgg register form
 *
 * @package Elgg
 * @subpackage Core
 */

/***********************************************************************
 * MODIFICATION LOG
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *
 * USER 		DATE 			DESCRIPTION
 * TLaw/ISal 	n/a 			GC Changes
 * CYu 			March 5 2014 	Second Email field for verification & code clean up & validate email addresses
 * CYu 			July 16 2014	clearer messages & code cleanup						
 * CYu 			Sept 19 2014 	adjusted textfield rules (no spaces for emails)
 * MBlondin 	Jan 25 2016 	Layout change
 * MBlondin 	Feb 08 2016 	Delete IE7 form
 * NickP        June 9 2016     Added function to the username generation ajax to provide link to password retrival if account already exists
 * CYu 			Aug 15 2016 	GCcollab - Student / Academic (w/Universities) & Public Servants
 * MWooff 		Jan 18 2017		Re-built for GCcollab-specific functions
 *
 ***********************************************************************/

$password = $password2 = '';
$username = get_input('e');
$email = get_input('e');
$name = get_input('n');
$site_url = elgg_get_site_url();

/*if (elgg_is_sticky_form('register')) {
	extract(elgg_get_sticky_values('register'));
	elgg_clear_sticky_form('register');
}*/

// Javascript
?>
<script type="text/javascript">
$(document).ready(function() {

	$("#user_type").change(function() {
		var type = $(this).val();
		$('.occupation-choices').hide();
		if (type == 'student' || type == 'academic') {
			$('.ministry-choices').hide();
			$('#institution').show();

			var institution = $('#institution-choices').val();
			if (institution == 'university') {
				$('#universities').show();
			} else if (institution == 'college') {
				$('#colleges').show();
			}
		} else if (type == 'federal') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#federal').show();
		} else if (type == 'provincial') {
			$('.student-choices').hide();
			$('#provincial').show();

			var province = $('#provincial-choices').val();
			$('#' + province.replace(/\s+/g, '-').toLowerCase()).show();
		} else if (type == 'municipal') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#municipal').show();
		} else if (type == 'international') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#international').show();
		} else {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#custom').show();
		}
	});

	$("#institution-choices").change(function() {
		var type = $(this).val();
		$('.student-choices').hide();
		if (type == 'university') {
			$('#universities').show();
		} else if (type == 'college') {
			$('#colleges').show();
		}
	});

	$("#provincial-choices").change(function() {
		var type = $(this).val();
		$('.ministry-choices').hide();
		$('#' + type.replace(/\s+/g, '-').toLowerCase()).show();
	});
});

// make sure the email address given does not contain invalid characters
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; 
    return re.test(email);
}
</script>

<!-- start of standard form -->
<div id="standard_version" class="row">

	<section class="col-md-6">
	<?php
		echo elgg_echo('gcRegister:email_notice') ;
		$js_disabled = false;
	?>
	</section>

	<?php
		function show_field( $field ){
			$enabled_fields = array('academic', 'student', 'federal', 'provincial');
			// $enabled_fields = array('academic', 'student', 'federal', 'provincial', 'municipal', 'international', 'community', 'business', 'media', 'other');
			return in_array($field, $enabled_fields);
		}
	?>

	<!-- Registration Form -->
	<section class="col-md-6">
		<div class="panel panel-default">
			<header class="panel-heading"> <h3 class="panel-title"><?php echo elgg_echo('gcRegister:form'); ?></h3> </header>
			<div class="panel-body mrgn-lft-md">

				<!-- Options for the users enabled in $enabled_fields above -->
				<div class="form-group">
					<label for="user_type" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:occupation'); ?></span></label>
					<font id="user_type_error" color="red"></font>
	    			<select id="user_type" name="user_type" class="form-control" >
	    				<?php if(show_field("federal")): ?><option selected="selected" value="federal"><?php echo elgg_echo('gcRegister:occupation:federal'); ?></option><?php endif; ?>
						<?php if(show_field("academic")): ?><option value="academic"><?php echo elgg_echo('gcRegister:occupation:academic'); ?></option><?php endif; ?>
	    				<?php if(show_field("student")): ?><option value="student"><?php echo elgg_echo('gcRegister:occupation:student'); ?></option><?php endif; ?>
	    				<?php if(show_field("provincial")): ?><option value="provincial"><?php echo elgg_echo('gcRegister:occupation:provincial'); ?></option><?php endif; ?>
	    				<?php if(show_field("municipal")): ?><option value="municipal"><?php echo elgg_echo('gcRegister:occupation:municipal'); ?></option><?php endif; ?>
	    				<?php if(show_field("international")): ?><option value="international"><?php echo elgg_echo('gcRegister:occupation:international'); ?></option><?php endif; ?>
	    				<?php if(show_field("community")): ?><option value="community"><?php echo elgg_echo('gcRegister:occupation:community'); ?></option><?php endif; ?>
	    				<?php if(show_field("business")): ?><option value="business"><?php echo elgg_echo('gcRegister:occupation:business'); ?></option><?php endif; ?>
	    				<?php if(show_field("media")): ?><option value="media"><?php echo elgg_echo('gcRegister:occupation:media'); ?></option><?php endif; ?>
	    				<?php if(show_field("other")): ?><option value="other"><?php echo elgg_echo('gcRegister:occupation:other'); ?></option><?php endif; ?>
	    			</select>
				</div>

<?php if(show_field("federal")): ?>

<?php
	$deptObj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'federal_departments',
	));
	$depts = get_entity($deptObj[0]->guid);

	$federal_departments = array();
	if (get_current_language() == 'en'){
		$federal_departments = json_decode($depts->federal_departments_en, true);
	} else {
		$federal_departments = json_decode($depts->federal_departments_fr, true);
	}

	// default to invalid value, so it encourages users to select
	$federal_choices = elgg_view('input/select', array(
		'name' => 'federal',
		'id' => 'federal-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $federal_departments),
	));
?>

				<div class="form-group occupation-choices" id="federal">
					<label for="federal-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:department'); ?></span></label>
					<?php echo $federal_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("academic") || show_field("student")): ?>

				<!-- Universities or Colleges -->
				<div class="form-group occupation-choices" id="institution" hidden>
					<label for="institution-choices" class="required"><span class="field-name"><?php echo elgg_echo('Institution'); ?></span></label>
					<select id="institution-choices" name="institution" class="form-control">
						<option selected="selected" value="default_invalid_value"> <?php echo elgg_echo('gcRegister:make_selection'); ?> </option>
						<option value="university"> <?php echo elgg_echo('gcRegister:university'); ?> </option>
						<option value="college"> <?php echo elgg_echo('gcRegister:college'); ?> </option>
					</select>
				</div>

<?php
	$uniObj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'universities',
	));
	$unis = get_entity($uniObj[0]->guid);

	$universities = array();
	if (get_current_language() == 'en'){
		$universities = json_decode($unis->universities_en, true);
	} else {
		$universities = json_decode($unis->universities_fr, true);
	}

	// default to invalid value, so it encourages users to select
	$university_choices = elgg_view('input/select', array(
		'name' => 'university',
		'id' => 'university-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $universities),
	));
?>

				<!-- Universities -->
				<div class="form-group student-choices" id="universities" hidden>
					<label for="university-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:university'); ?></span></label>
					<?php echo $university_choices ?>
				</div>

<?php
	$colObj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'colleges',
	));
	$cols = get_entity($colObj[0]->guid);

	$colleges = array();
	if (get_current_language() == 'en'){
		$colleges = json_decode($cols->colleges_en, true);
	} else {
		$colleges = json_decode($cols->colleges_fr, true);
	}

	// default to invalid value, so it encourages users to select
	$college_choices = elgg_view('input/select', array(
		'name' => 'college',
		'id' => 'college-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $colleges),
	));
?>

				<!-- Colleges -->
				<div class="form-group student-choices" id="colleges" hidden>
					<label for="college-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:college'); ?></span></label>
					<?php echo $college_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("provincial")): ?>

<?php
	$provObj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'provinces',
	));
	$provs = get_entity($provObj[0]->guid);

	$provincial_departments = array();
	if (get_current_language() == 'en'){
		$provincial_departments = json_decode($provs->provinces_en, true);
	} else {
		$provincial_departments = json_decode($provs->provinces_fr, true);
	}

	// default to invalid value, so it encourages users to select
	$provincial_choices = elgg_view('input/select', array(
		'name' => 'provincial',
		'id' => 'provincial-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $provincial_departments),
	));
?>

				<div class="form-group occupation-choices" id="provincial" hidden>
					<label for="provincial-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:province'); ?></span></label>
					<?php echo $provincial_choices ?>
				</div>

<?php
	$provinces = array("Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Northwest Territories", "Nova Scotia", "Nunavut", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan", "Yukon");

	$minObj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'ministries',
	));
	$mins = get_entity($minObj[0]->guid);

	$ministries = array();
	if (get_current_language() == 'en'){
		$ministries = json_decode($mins->ministries_en, true);
	} else {
		$ministries = json_decode($mins->ministries_fr, true);
	}

	foreach($provinces as $province){
		$prov_id = str_replace(" ", "-", strtolower($province));
		echo '<div class="form-group ministry-choices" id="' . $prov_id . '" hidden><label for="' . $prov_id . '-choices" class="required"><span class="field-name">' . elgg_echo('gcRegister:ministry') . '</span></label>';
		echo elgg_view('input/select', array(
			'name' => 'ministry',
			'id' => $prov_id . '-choices',
	        'class' => 'form-control',
			'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $ministries[$province]),
		));
		echo '</div>';
	}
?>

<?php endif; ?>

<?php if(show_field("municipal")): ?>

<?php
	$municipal_governments = array();
	if (get_current_language() == 'en'){
		$municipal_governments = array("one", "two", "three");
	} else {
		$municipal_governments = array("un", "deux", "trois");
	}

	// default to invalid value, so it encourages users to select
	$municipal_choices = elgg_view('input/select', array(
		'name' => 'municipal',
		'id' => 'municipal-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $municipal_governments),
	));
?>

				<div class="form-group occupation-choices" id="municipal" hidden>
					<label for="municipal-choices" class="required"><span class="field-name"><?php echo elgg_echo('Municipal'); ?></span></label>
					<?php echo $municipal_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("international")): ?>

<?php
	$international_governments = array();
	if (get_current_language() == 'en'){
		$international_governments = array("one", "two", "three");
	} else {
		$international_governments = array("un", "deux", "trois");
	}

	// default to invalid value, so it encourages users to select
	$international_choices = elgg_view('input/select', array(
		'name' => 'international',
		'id' => 'international-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $international_governments),
	));
?>

				<div class="form-group occupation-choices" id="international" hidden>
					<label for="international-choices" class="required"><span class="field-name"><?php echo elgg_echo('International'); ?></span></label>
					<?php echo $international_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("community") || show_field("business") || show_field("media") || show_field("other")): ?>

<?php
	$custom_occupation = elgg_view('input/text', array(
		'name' => 'custom_occupation',
		'id' => 'custom_occupation',
        'class' => 'form-control',
	));
?>

				<div class="form-group occupation-choices" id="custom" hidden>
					<label for="custom_occupation" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:custom'); ?></span></label>
					<?php echo $custom_occupation ?>
				</div>

<?php endif; ?>
				
				<!-- Display Name -->
				<div class="form-group">
					<label for="name" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:display_name'); ?></span></label>
					<font id="name_error" color="red"></font>
<?php
			echo elgg_view('input/text', array(
				'name' => 'name',
				'id' => 'name',
		        'class' => 'form-control display_name',
				'value' => $name,
			));
?>
				</div>
		    	<div class="alert alert-info"><?php echo elgg_echo('gcRegister:display_name_notice'); ?></div>

				<!-- Email -->
				<div class="form-group">
					<label for="email" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:email'); ?></span></label>
	    			<font id="email_error" color="red"></font>
					<input id="email" class="form-control" type="text" value='<?php echo $email ?>' name="email" onBlur="" />

	    		<script>	
	        		$('#email').blur(function () {
	            		elgg.action( 'register/ajax', {
							data: {
								email: $('#email').val()
							},
							success: function (x) {
			    				if (x.output == "<?php echo '> ' . elgg_echo('gcRegister:email_in_use'); ?>") {
					                $('#email_error').html("<?php echo elgg_echo('registration:userexists'); ?>").removeClass('hidden');
			    				} else if (x.output == "<?php echo '> ' . elgg_echo('gcRegister:invalid_email'); ?>") {
					                $('#email_error').text("<?php echo elgg_echo('gcRegister:invalid_email'); ?>").removeClass('hidden');
			    				} else {
			    					$('#email_error').addClass('hidden');
			    				}
							},   
						});
	        		});

	        		$('#name').blur(function () {
	        			elgg.action( 'register/ajax', {
							data: {
								name: $('#name').val()
							},
							success: function (x) {
			    				$('.username_test').val(x.output);
							},   
						});
	        		});
	    		</script>

				</div> <!-- end form-group div -->
		    	<div class="return_message"></div>

				<!-- Username (auto-generate) -->
				<div class="form-group" style="display:none">
					<label for="username" class="required" ><span class="field-name"><?php echo elgg_echo('gcRegister:username'); ?></span> </label> 
				    <div class="already-registered-message mrgn-bttm-sm"><span class="label label-danger tags mrgn-bttm-sm"></span></div>
<?php
			echo elgg_view('input/text', array(
				'name' => 'username',
				'id' => 'username',
		        'class' => 'username_test form-control',
				// 'readonly' => 'readonly',
				'value' => $username,
			));
?>
				</div>

				<!-- Password -->
				<div class="form-group">
					<label for="password" class="required"><span class="field-name"><span class="field-name"><?php echo elgg_echo('gcRegister:password_initial'); ?></span> </label>
					<font id="password_initial_error" color="red"></font>
<?php
			echo elgg_view('input/password', array(
				'name' => 'password',
				'id' => 'password',
		        'class'=>'password_test form-control',
				'value' => $password,
			));
?>
				</div>

				<!-- Secondary Password -->
				<div class="form-group">
					<label for="password2" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:password_secondary'); ?></span> </label>
				    <font id="password_secondary_error" color="red"></font>
<?php
			echo elgg_view('input/password', array(
				'name' => 'password2',
				'value' => $password2,
				'id' => 'password2',
		        'class'=>'password2_test form-control',
			));
?>
				</div>

			    <div class="checkbox"> <label><input type="checkbox" value="1" name="toc2" id="toc2" /><?php echo elgg_echo('gcRegister:terms_and_conditions')?></label> </div>

<?php
			// view to extend to add more fields to the registration form
			echo elgg_view('register/extend', $vars);

			// Add captcha hook
			echo elgg_view('input/captcha', $vars);
			echo '<div class="elgg-foot">';
			echo elgg_view('input/hidden', array('name' => 'friend_guid', 'value' => $vars['friend_guid']));
			echo elgg_view('input/hidden', array('name' => 'invitecode', 'value' => $vars['invitecode']));

			// note: disable
			echo elgg_view('input/submit', array(
			    'name' => 'submit',
			    'value' => elgg_echo('gcRegister:register'),
			    'id' => 'submit',
			    'class'=>'submit_test btn-primary',));
			echo '</div>';
?>
	            
			</div>
		</div>
	</section>

<script>
	// check if the initial email input is empty, then proceed to validate email
    $('#email').on("focusout", function() {
    	var val = $(this).val();
        if ( val === '' ) {
        	var c_err_msg = '<?php echo elgg_echo('gcRegister:empty_field') ?>';
            document.getElementById('email_error').innerHTML = c_err_msg;
        } else if ( val !== '' ) {
            document.getElementById('email_error').innerHTML = '';
            
            if (!validateEmail(val)) {
            	var c_err_msg = '<?php echo elgg_echo('gcRegister:invalid_email') ?>';
            	document.getElementById('email_error').innerHTML = c_err_msg;
            }
        }
    });

    $('.password_test').on("focusout", function() {
    	var val = $(this).val();
	    if ( val === '' ) {
	    	var c_err_msg = "<?php echo elgg_echo('gcRegister:empty_field') ?>";
	        document.getElementById('password_initial_error').innerHTML = c_err_msg;
	    } else if ( val !== '' ) {
	        document.getElementById('password_initial_error').innerHTML = '';
	    }

        var val_2 = $('#password2').val();
        if (val_2 == val) {
	        document.getElementById('password_secondary_error').innerHTML = '';
        } else if (val_2 !== '' && val_2 != val) {
            var c_err_msg = "<?php echo elgg_echo('gcRegister:mismatch') ?>";
	        document.getElementById('password_secondary_error').innerHTML = c_err_msg;
        }
	});	
    
    $('#password2').on("focusout", function() {
    	var val = $(this).val();
	    if ( val === '' ) {
	    	var c_err_msg = "<?php echo elgg_echo('gcRegister:empty_field') ?>";
	        document.getElementById('password_secondary_error').innerHTML = c_err_msg;
	    } else if ( val !== '' ) {
	        document.getElementById('password_secondary_error').innerHTML = '';
	        
	        var val2 = $('.password_test').val();
	        if (val2 != val) {
	        	var c_err_msg = "<?php echo elgg_echo('gcRegister:mismatch') ?>";
	        	document.getElementById('password_secondary_error').innerHTML = c_err_msg;
	        }
	    }
	});
    
    $('#name').on("focusout", function() {
    	var val = $(this).val();
        if ( val === '' ) {
        	var c_err_msg = '<?php echo elgg_echo('gcRegister:empty_field') ?>';
            document.getElementById('name_error').innerHTML = c_err_msg;
        } else if ( val !== '' ) {
            document.getElementById('name_error').innerHTML = '';
        }
    });

    $("form.elgg-form-register").on("submit", function(){
	    $(".occupation-choices select:not(:visible), .occupation-choices input:not(:visible), .student-choices select:not(:visible), .ministry-choices select:not(:visible)").attr('disabled', 'disabled');
	    $(".occupation-choices select:visible, .occupation-choices input:visible, .student-choices select:visible, .ministry-choices select:visible").removeAttr('disabled');
	});
</script>

</div>
