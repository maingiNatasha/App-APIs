<?php
require "Database.php";

// Create a new instance of the Database class so that we can access its methods
$db = new Database();

// Check if the required fields have been submitted
if (isset($_POST['role']) && isset($_POST['username']) && isset($_POST['password'])) {

    // Check the connection
    if ($db->dbConnect()) {

        // Call the login method to check if login was successful
        $loginResult = $db->logIn($_POST['role'], $_POST['username'], $_POST['password']);

        if ($loginResult === true) {
            // Successful login

            if ($_POST['role'] == "Supplier") {
                // Retrieve the supplierId and username based on the username
                $result = $db->getSupplierInfo($_POST['username']);

                if ($result !== false) {
                    $supplierId = $result['supplierId'];
                    $username = $result['username'];
                    $fullname = $result['fullname'];
                    $email = $result['email'];

                    // Create a JSON response
                    $response = array(
                        "status" => "success",
                        "message" => "Login Success",
                        "supplierId" => $supplierId,
                        "username" => $username,
                        "fullname" => $fullname,
                        "email" => $email
                    );
                } else {
                    // Supplier ID not found
                    $response = array(
                        "status" => "error",
                        "message" => "Supplier ID not found"
                    );
                }
            } else {
                // Retrieve the farmerId and username based on the username
                $result = $db->getFarmerInfo($_POST['username']);

                if ($result !== false) {
                    $farmerId = $result['farmerId'];
                    $username = $result['username'];
                    $fullname = $result['fullname'];
                    $email = $result['email'];

                    // Create a JSON response
                    $response = array(
                        "status" => "success",
                        "message" => "Login Success",
                        "farmerId" => $farmerId,
                        "username" => $username,
                        "fullname" => $fullname,
                        "email" => $email
                    );
                } else {
                    // Farmer ID not found
                    $response = array(
                        "status" => "error",
                        "message" => "Farmer ID not found"
                    );
                }
            }
        } else {
            // Incorrect username or password
            $response = array(
                "status" => "error",
                "message" => "Incorrect username or password"
            );
        }

        // Return the JSON response
        echo json_encode($response);

    } else {
        echo "Error: Database connection";
    }

} else {
    echo "All fields are required";
}
?>


