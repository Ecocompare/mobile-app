<?php
/*
* getProduct.php
* get information of the product_id param
*/

header('Content-Type: application/json');

function arrondir($valeur) {
	$entier=intval($valeur);
	$decimale=$valeur-$entier;
	if ($decimale>0.5) return round($entier+1,1);
	if ($decimale<0.5) return round($entier,1);
	if ($decimale==0.5) return round($valeur,1);
	
}

include('../models/Db.php');

$db = new db();


if(isset($_GET['id'])){
	echo json_encode($db->getProduct(intval($_GET['id'])));	
}


?>