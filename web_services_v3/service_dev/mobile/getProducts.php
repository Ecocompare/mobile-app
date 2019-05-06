<?php

/*
* getProduct.php
* get the new products
*/

header('Content-Type: application/json');

include('../models/Db.php');

$db = new db();

echo json_encode($db->getProducts("","pubdate DESC"));

?>