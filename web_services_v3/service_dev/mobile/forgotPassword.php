<?php

/*
* forgotPassword.php
* Send a mail for check if user really want reset his password
*/

header('Content-Type: application/json');

include('../models/Db.php');

include('mail.php');

$db = new db();

if(isset($_GET['email'])){

	$results = $db->getMobileUserByEmail($_GET['email']);
	
	if($results==null){
		echo json_encode(false);
	}
	else{
		//got already a token
		if(!empty($results['token'])){
			$token = $results['token'];
		}
		else{
		
			$token = uniqid(); //26 chars for dodge collision
			$token.= uniqid(); 
			
			$params = array(
		      'token' => $token,
  			);
			
  			$db->updateMobileUser($results['id'], $params);
		}

		mailValidationPasswordForgot($_GET['email'], $token); //Send the mail
		
		echo json_encode(true);		
	}
}
?>