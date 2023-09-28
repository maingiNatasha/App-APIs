<?php
require "Database.php";

// Create a new instance of the Database class
$db = new Database();

if (isset($_POST['itemName']) && isset($_POST['itemType']) && isset($_POST['itemQuantity']) && isset($_POST['itemMinQuantity']) && isset($_POST['farmerId'])) {

    // Extract the values from the POST request
    $itemName = $_POST['itemName'];
    $itemType = $_POST['itemType'];
    $itemQuantity = $_POST['itemQuantity'];
    $itemMinQuantity = $_POST['itemMinQuantity'];

    // Perform the validations
    if (empty($itemName) || !preg_match('/^[A-Za-z\s]+$/', $itemName)) {
        echo "Item Name is required and should only contain alphabetic characters and spaces";
        return;
    }

    if (empty($itemType) || !preg_match('/^[A-Za-z\s]+$/', $itemType)) {
        echo "Item Type is required and should only contain alphabetic characters and spaces";
        return;
    }

    if (!is_numeric($itemQuantity)) {
        echo "Item Quantity should be a numeric value";
        return;
    }

    if (!is_numeric($itemMinQuantity)) {
        echo "Item Min Quantity should be a numeric value";
        return;
    }

    // Check database connection
    if ($db->dbConnect()) {

        // Call the add inventory method to check if it was successful
        if ($db->addInventory("inventory", $itemName, $itemType, $itemQuantity, $itemMinQuantity, $_POST['itemUnit'], $_POST['farmerId'])) {
            echo "Item Added";
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