<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "Database.php";

// Create new instance of the Database class to allow use of its methods for database operations
$db = new Database();

// Check if the required fields have been submitted
if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {

    // Check database connection
    if ($db->dbConnect()) {

        // Check if username already exists
        $username = $_POST['username'];
        $role = $_POST['role'];
        
        if ($db->checkUsernameExists("users_" . $role, $username)) {
            echo "Username already exists";
            return;
        }
        
        // Validation functions
        function validateUsername($username) 
        {
            // Return true if the field value passes all the validations
            return !preg_match('/^[!@#$%^&*()?]+$/', $username) && !ctype_digit($username) && !empty(trim($username));
        }
        
        function validateFullname($fullname) 
        {
            // Return true if the field value passes all the validations
            return !preg_match('/^[!@#$%^&*()?]+$/', $fullname) && !ctype_digit($fullname) && !empty(trim($fullname));
        }
        
        function validateEmail($email)
        {
            // Return true if the field value passes all the validations
            return filter_var($email, FILTER_VALIDATE_EMAIL) && !empty(trim($email));
        }
        
        function validatePassword($password)
        {
            // Return true if the field value passes all the validations
            return strlen($password) >= 4 && !empty(trim($password));
        }

        if ($db->checkUsernameExists("users_" . $role, $username)) {
            echo "Username already exists";
            return;
        }

        // Validate the fullname, username, email, and password
        $fullname = $_POST['fullname'];
        if (!validateFullname($fullname)) {
            echo "Invalid fullname";
            return;
        }

        $email = $_POST['email'];
        if (!validateEmail($email)) {
            echo "Invalid email";
            return;
        }

        if (!validateUsername($username)) {
            echo "Invalid username";
            return;
        }

        $password = $_POST['password'];
        if (!validatePassword($password)) {
            echo "Invalid password";
            return;
        }

        // Call the signUp method to check if sign up was successful
        if ($db->signUp($role, $fullname, $email, $username, $password)) {
            echo "Sign Up Success";
        } else {
            echo "Sign up Failed";
        }

    } else {
        echo "Error: Database connection";
    }

} else {
    echo "All fields are required";
}

?>