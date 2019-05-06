<?php
/*
* getUserInfo.php
* get information from user_id in param
*/
header('Content-Type: application/json');

include('../models/Db.php');

$db = new db();

if(isset($_GET['id'])){
	echo json_encode($db->getMobileUser($_GET['id']));
}


?>