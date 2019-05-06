<?php

/*
* checkUser.php
* check if user exist
* Case 1 : ok
* Case 2 : Creation
*/

header('Content-Type: application/json');

include('../models/Db.php');

include('mail.php');

$db = new db();

if(isset($_GET['email'])&&isset($_GET['pwd'])){

	$email = $_GET['email'];
	$pwd = $_GET['pwd'];

	//Exist
	if($db->checkMobileUser($email, $pwd)!= null){
		echo json_encode($db->getMobileUserByEmail($email));
	}
	else{
		//Creation
		if($db->getMobileUserByEmail($email)==null){
			
			$token = uniqid(); 
			$token.= uniqid(); //26 chars token

			$params = array(
		      'email' => $email, 
		      'pwd' => sha1($pwd),
		      'token' => $token,
		      'isValid' => false,
		      'inscription' => date("Y-m-d H:i:s"),
		      'useragent' => $_SERVER['HTTP_USER_AGENT'],
  			);

			$db->createMobileUser($params);

			mailValidation($email, $token); //Send validation mail

			$user = $db->getMobileUserByEmail($email);
			
			//Create a new row in iphone_users_stat
			$params = array(
		      'user_id' => $user['id'], 
		      'total_scan' => null,
		      'total_resp_scan' => null,
		      'month_scan' => null,
		      'month_resp_scan' => null,
		      'nbwin' => null,
  			);

			$db->createUserStats($params);

			echo json_encode($user);
		}
		else{
			echo json_encode(false);
		}
	}
}
else if(isset($_GET['id'])&&isset($_GET['resend'])){ //Resend Email process
	
	$results = $db->getMobileUser($_GET['id']);
	
	if($results != null && !empty($results['token'])){
	
		mailValidation($results['email'], $results['token']);
		
		echo json_encode(true);
		
	}
}

?>