<?php

/*
* setUserInfo.php
* update the user info in database
*/

header('Content-Type: application/json');

include('../models/Db.php');

$db = new db();

if(isset($_GET['id'])&&isset($_GET['name'])/*&&isset($_GET['parrain'])*/&&isset($_GET['gender'])&&isset($_GET['tel'])&&isset($_GET['birthyear'])&&isset($_GET['postalcode'])&&isset($_GET['ville'])&&isset($_GET['address'])&&isset($_GET['job'])&&isset($_GET['child'])){
	
	$params = array(
		      'name' => $_GET['name'],
		      //'parrain' => $_GET['parrain'],
		      'gender' => $_GET['gender'],
		      'tel' => $_GET['tel'],
		      'birthyear' => $_GET['birthyear'],
		      'ville' => $_GET['ville'],
		      'postalcode' => $_GET['postalcode'],
		      'address' => $_GET['address'],
		      'job' => $_GET['job'],
		      'nb_child' => $_GET['child'],
  	);
	
	$db->updateMobileUser($_GET['id'], $params);
	
	echo json_encode(true);
}
else{
	echo json_encode(false);
}


?>