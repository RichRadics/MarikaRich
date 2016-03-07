<?php

// ** MySQL settings - You can get this info from your web host ** //
	// This include file defines the 4 standard DB connection variables
	//include 'login-marikarich.php';
	include(dirname(__FILE__)."/login-marikarichdb.php");
	
	// ** Email confirmation settings
	define('EMAIL_SEND', true);    					// set to true to enable email confirmation to be sent on form submision
	
	define('EMAIL_FROM', "email@marikarich.com"); 	// confirmation from email address
	define('EMAIL_BODY_ATTEND', "Thank you for your response.\r\n\r\nWe look forward to seeing you soon!\r\n\r\nCheers,\r\n\r\nMarika and Rich"); 	// confirmation email body for those that can attend
	define('EMAIL_BODY_CANTATTEND', "Thank you for your response.\r\n\r\nWe are sorry to hear you cannot attend.\r\n\r\nCheers,\r\n\r\nMarika and Rich"); 	// confirmation email body for those that can't attend

	// ** Event Hosts
	define('EVENT_HOSTS', "Marika and Rich");

	function get_ip() {
		if ($_SERVER['HTTP_X_FORWARD_FOR']) {
			return $_SERVER['HTTP_X_FORWARD_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
	function default_val(&$var, $default) {
		if ($var=='') {
			$var = $default;
		}
	}
	
	function check_mail($email) {
		if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
			list($username,$domain)=split('@',$email);
			if(!checkdnsrr($domain,'MX')) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	$data = $_POST;
	
	//retrieve will equal true if it is a retrieve, false if it is an existing modification
	$ret = ($data['rsvptype'] == "retrieve");
	
	//value to store a successful update/new rsvp
	$success = false;
	
	//value to store the submitbutton text
	$submitbutton = 'Submit';
	
	//value to store if the server checks are passed
	
	//value to store the return message
	$message = '';
	$data['fullname0'] = $_POST['fullname0'];
	$data['attendance'] = $_POST['attendance'];
	$data['vegetarian0'] = $_POST['vegetarian0'];
	$data['phonenumber'] = $_POST['phonenumber'];
	$data['comments'] = $_POST['comments'];
	$data['guests'] = $_POST['guests'];
	$data['fullname1'] = $_POST['fullname1'];
	$data['vegetarian1'] = (isset($_POST['vegetarian1'])) ? 1 : 0;
	$data['fullname2'] = $_POST['fullname2'];
	$data['vegetarian2'] = (isset($_POST['vegetarian2'])) ? 1 : 0;
	$data['fullname3'] = $_POST['fullname3'];
	$data['vegetarian3'] = (isset($_POST['vegetarian3'])) ? 1 : 0;
	$data['fullname4'] = $_POST['fullname4'];
	$data['vegetarian4'] = (isset($_POST['vegetarian4'])) ? 1 : 0;
	$data['fullname5'] = $_POST['fullname5'];
	$data['vegetarian5'] = (isset($_POST['vegetarian5'])) ? 1 : 0;
	$data['fullname6'] = $_POST['fullname6'];
	$data['vegetarian6'] = (isset($_POST['vegetarian6'])) ? 1 : 0;
	$data['song'] = $_POST['song'];

	//submit the data
	$passed = false;
	//lets perform some checks
	if (check_mail($data['email'])) {
		//test if name filled
		if ($data['fullname0']) {
			//test if full name
			if (strstr($data['fullname0']," ")) {
				$passed = true;
			} else {
				$message = "Please enter your full name.";
			}
		} else {	
			$message = "Please enter your full name.";
		}
	} else {
		$message = "Please enter valid email address.";
	}

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or error_log('Error connecting to mysql',3,'../my-errors.log');
	mysql_select_db(DB_NAME);
	
	if ($passed) { 
		//make sure the guest values match the selected guests
		
		$query = sprintf("INSERT INTO rsvp SET IP = '%s', EMAIL = '%s', NAME0 = '%s', ATTEND = %d, VEG0 = %d, PHONENUMBER = '%s', COMMENTS = '%s', GUESTS = %d, NAME1 = '%s', VEG1 = %d, NAME2 = '%s', VEG2 = %d, NAME3 = '%s', VEG3 = %d, NAME4 = '%s', VEG4 = %d, NAME5 = '%s', VEG5 = %d, NAME6 = '%s', VEG6='%s', SONG = '%s'",
			mysql_real_escape_string(get_ip()),
			mysql_real_escape_string($data['email']),
			mysql_real_escape_string($data['fullname0']),
			mysql_real_escape_string($data['attendance']),
			mysql_real_escape_string($data['vegetarian0']),
			mysql_real_escape_string($data['phonenumber']),
			mysql_real_escape_string($data['comments']),
			mysql_real_escape_string($data['guests']),
			mysql_real_escape_string($data['fullname1']),
			mysql_real_escape_string($data['vegetarian1']),
			mysql_real_escape_string($data['fullname2']),
			mysql_real_escape_string($data['vegetarian2']),
			mysql_real_escape_string($data['fullname3']),
			mysql_real_escape_string($data['vegetarian3']),
			mysql_real_escape_string($data['fullname4']),
			mysql_real_escape_string($data['vegetarian4']),
			mysql_real_escape_string($data['fullname5']),
			mysql_real_escape_string($data['vegetarian5']),
			mysql_real_escape_string($data['fullname6']),
			mysql_real_escape_string($data['vegetarian6']),
			mysql_real_escape_string($data['song']));
		
		mysql_query($query);
		
		$success = true;
		//send out a confirmation email 
		$to = $data['email'];
		$subject = "RSVP confirmation";
		if ($data['attendance']=='1') {
			$body = "Dear {$data['fullname0']},\r\n\r\n".EMAIL_BODY_ATTEND;
			$message = "Thank you {$data['fullname0']}, we look forward to seeing you there.";
		} else {
			$body = "Dear {$data['fullname0']},\r\n\r\n".EMAIL_BODY_CANTATTEND;
			$message = "Thank you {$data['fullname0']}, we are sorry to hear you cannot attend.";
		}
		$headers = "From: ".EMAIL_FROM."\r\n";
		
		if (EMAIL_SEND) {
			mail($to, $subject, $body, $headers);
		}
	}


	
	$form_data = array(); //Pass back the data to `form.php`
	$form_data['success'] = true;
	$form_data['posted'] = $data;
	echo json_encode($form_data);
	mysql_close($conn);

?>