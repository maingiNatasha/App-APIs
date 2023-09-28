<?php

require "Database.php";

$db = new Database();

if ($db->dbConnect()) {
    if(isset($_POST['orderId']) && isset($_POST['orderStatus'])) {
        if($db->updateStatus("purchase_order", $_POST['orderId'], $_POST['orderStatus'])) {
            echo "Order status updated";
        }
        else echo "Status update failed";

    }else echo "Order Id missing";
    
}else echo "Error: Database connection";

?>