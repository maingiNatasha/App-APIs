<?php
require "Database.php";

//create new instance of Database class so that we can acces its methods
$db = new Database();

if ($db->dbConnect()) {
    $db->displaySuppliers("suppliers_items");
} else {
    echo "Error: Database connection";
}
?>