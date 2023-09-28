<?php

require "Database.php";

$db = new Database();

if ($db->dbConnect()) {
    if(isset($_POST['supplierItemId'])) {
        
        if($db->deleteSupplierItem("suppliers_items", $_POST['supplierItemId'])) {
            echo "Item Removed";
        } else {
            echo "Failed to remove item";
        }
    }else echo "Item Id missing";
}else echo "Error: Database connection";

?>