<?php
require "Database.php";

// Create a new instance of the Database class
$db = new Database();

// Check if the required fields have been submitted
if (isset($_POST['itemName']) && isset($_POST['itemType']) && isset($_POST['itemQuantity']) && isset($_POST['itemMinQuantity']) && isset($_POST['itemUnit'])) {

    // Extract the values from the POST request
    $itemName = $_POST['itemName'];
    $itemType = $_POST['itemType'];
    $itemQuantity = $_POST['itemQuantity'];
    $itemMinQuantity = $_POST['itemMinQuantity'];

    // Perform the validations
    if (empty($itemName) || !preg_match('/^[A-Za-z\s]+$/', $itemName)) {
        echo "Item Name should only contain alphabetic characters and spaces";
        return;
    }

    if (empty($itemType) || !preg_match('/^[A-Za-z\s]+$/', $itemType)) {
        echo "Item Type should only contain alphabetic characters and spaces";
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

    // Add further validations as needed

    // Check database connection
    if ($db->dbConnect()) {

        if ($db->updateItem("inventory", $itemName, $itemType, $itemQuantity, $itemMinQuantity, $_POST['itemUnit'], $_POST['itemId'])) {
            echo "Update Successful";
        } else {
            echo "Update Failed";
        }

    } else {
        echo "Error: Database connection";
    }

} else {
    echo "All fields are required";
}
?>
