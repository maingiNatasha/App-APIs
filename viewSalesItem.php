<?php
require "Database.php";

// Create a new instance of the Database class so that we can access its methods
$db = new Database();

if ($db->dbConnect()) {
    $db->displaySalesItem("suppliers_items");
} 
else {
    echo "Error: Database connection";
}

?>
