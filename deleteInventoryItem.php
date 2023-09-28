<?php

require "Database.php";

$db = new Database();

if ($db->dbConnect()) {
    if(isset($_POST['itemId'])) {
        
        if($db->deleteInventoryItem("inventory", $_POST['itemId'])) {
            echo "Item Removed";
        } else {
            echo "Failed to remove item";
        }
    }else echo "Item Id missing";
}else echo "Error: Database connection";

?>