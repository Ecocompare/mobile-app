<?php
/*
* getProductEan.php
* get product information from param EAN
* Search in products -> if not found -> iphone_eans_desc
*/

header('Content-Type: application/json');

function arrondir($valeur) {
	$entier=intval($valeur);
	$decimale=$valeur-$entier;
	if ($decimale>0.5) return round($entier+1,1);
	if ($decimale<0.5) return round($entier,1);
	if ($decimale==0.5) return round($valeur,1);
	
}
require_once('../models/Db.php');

$db = new Db();

$ean = null;

if(isset($_GET['ean'])) {
  $ean = mysql_escape_string($_GET['ean']);
}

if($ean == null) {
  exit();
}  

//Si ean
if($ean != null){

  $query = "SELECT * FROM products where INSTR(EAN, '$ean')>0;";
  $results = mysql_query($query);

  //Si ean dans product
  if($product = mysql_fetch_assoc($results)){
  	$id = $product['id'];
  	$product['categories'] = $db->getProductCategories($id);
	$product['labels'] = $db->getProductLabels($id);
	$product['subratings'] = $db->getProductSubratings($id);
	$product['subratingsdetail'] = $db->getProductSubratings($id, true,false,false);
	$product['subratingsfeedback'] = $db->getProductSubratings($id, false,true,false);
	$product['subratingslabel'] = $db->getProductSubratingsComments($id);
	$product['indicators']=$db->getProductIndicator($id);
   	echo json_encode($product);
  }
  else
  {
  	$query = "SELECT * FROM `iphone_eans_desc` WHERE `description` != '' AND ean like '$ean'";
    $results = mysql_query($query);
    if($product = mysql_fetch_assoc($results)){
    	echo json_encode($product['description'].'-'.$product['marque']);
    }
    else{
    	echo json_encode(false);
    }
  }
}
?>