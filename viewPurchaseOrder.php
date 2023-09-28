<?php
require "Database.php";

//create new instance of Database class so that we can acces its methods
$db = new Database();

if ($db->dbConnect()) {
    $db->viewPurchase("purchase_order");
} else {
    echo "Error: Database connection";
}
?>
