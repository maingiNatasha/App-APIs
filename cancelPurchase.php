<?php

require "Database.php";

$db = new Database();

if ($db->dbConnect()) {
    if(isset($_POST['orderId'])) {
        
        if($db->cancelPurchase("purchase_order", $_POST['orderId'])) {
            echo "Order Cancelled";
        } else {
            echo "Failed to cancel order";
        }
    }else echo "Order Id missing";
}else echo "Error: Database connection";

?>