<?php
require "Database.php";

// Create a new instance of the Database class so that we can access its methods
$db = new Database();

if (isset($_POST['orderItemName']) && isset($_POST['orderItemPrice']) && isset($_POST['orderItemQuantity']) && isset($_POST['orderItemUnit']) && isset($_POST['orderTotalPrice']) && isset($_POST['orderSupplierName']) && isset($_POST['paymentMethod']) && isset($_POST['orderCustomerName']) && isset($_POST['farmerId'])) {
    // Check connection
    if ($db->dbConnect()) {
        // Get the current date
        $date = date('Y-m-d');
        // Call the addPurchase method with the date parameter
        if ($db->addPurchase("purchase_order", $date, $_POST['orderItemName'], $_POST['orderItemPrice'], $_POST['orderItemQuantity'], $_POST['orderItemUnit'], $_POST['orderTotalPrice'], $_POST['orderSupplierName'], $_POST['paymentMethod'], $_POST['orderCustomerName'], $_POST['farmerId'])) {
            echo "Order Added";
        } else {
            echo "Process Failed";
        }
    } else {
        echo "Error: Database connection";
    }
} else {
    echo "All fields are required";
}
?>
