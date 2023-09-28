<?php
require "Database.php";

// Create a new instance of the Database class
$db = new Database();

if (isset($_POST['supplierItemId']) && isset($_POST['supplierName']) && isset($_POST['supplierItemName']) && isset($_POST['supplierItemPrice']) && isset($_POST['supplierItemDescription'])) {

	// Extract the values from the POST request
    $supplierItemId = $_POST['supplierItemId'];
    $supplierName = $_POST['supplierName'];
    $supplierItemName = $_POST['supplierItemName'];
    $supplierItemPrice = $_POST['supplierItemPrice'];
    $supplierItemDescription = $_POST['supplierItemDescription'];

    // Perform the validations
    if (empty($supplierName) || !preg_match('/^[A-Za-z\s]+$/', $supplierName)) {
        echo "Supplier Name is required and should only contain alphabetic characters and spaces";
        return;
    }

    if (empty($supplierItemName) || !preg_match('/^[A-Za-z\s]+$/', $supplierItemName)) {
        echo "Item Name is required and should only contain alphabetic characters and spaces";
        return;
    }

    if (!is_numeric($supplierItemPrice)) {
        echo "Item Price should be a numeric value";
        return;
    }

    // Check database connection
    if ($db->dbConnect()) {
    	if ($db->updateSupplierItem("suppliers_items", $supplierItemId, $supplierName, $supplierItemName, $supplierItemPrice, $supplierItemDescription)) {
            echo "Item Updated";
        } else {
            echo "Update Failed";
        }

    }else {
        echo "Error: Database connection";
    }

}else {
    echo "All fields are required";
}

?>