<?php
require "Database.php";

// Create a new instance of the Database class to access its methods
$db = new Database();

if ($db->dbConnect()) {
    // Database connection successful

        // Operation 2: Add new items to inventory
        if ($db->updateNewInventory()) {
            echo "New item added to inventory";
        } else {
            echo "Error adding new items to inventory.";
        }
    
} else {
    // Database connection failed
    echo "Error connecting to the database.";
}

?>
