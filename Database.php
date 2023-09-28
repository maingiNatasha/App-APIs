<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    //public properties that can be accessed outside the class
    public $connect;
    public $data;
    private $sql;

    public function __construct()
    {
        //Initialize properties of the class
        $this->data = null;
        $this->sql = null; 
    }

    //method to establish connection to the database
    function dbConnect()
    {
        $this->connect = new mysqli('localhost', 'root', '', 'my_db');
        return $this->connect;
    }

    //method to sanitize and escape data before using it in database queries
    function prepareData($data)
    {
        return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
    }

    //method to validate username by checking if it exists
    function checkUsernameExists($table, $username)
    {
        $username = $this->prepareData($username);
        $this->sql = "SELECT * FROM $table WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        //return true if username exists
        if (mysqli_num_rows($result) !=0) {
            return true;
        }
    }

    //method responsible for user authentication
    function logIn($role, $username, $password)
    {
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $role = strtolower($role); // Convert the role to lowercase
        $table = "users_" . $role;

        $this->sql = "SELECT * FROM $table WHERE username = '$username'";

        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) != 0) {
            $dbusername = $row['username'];
            $dbpassword = $row['password'];

            if ($dbusername == $username && password_verify($password, $dbpassword)) {
                $login = true;
            } else $login = false;

        } else $login = false;

        return $login;
    }
    
    //method for adding sign up info to respective tables
    function signUp($role, $fullname, $email, $username, $password)
    {
        $fullname = $this->prepareData($fullname);
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $email = $this->prepareData($email);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $role = strtolower($role); // Convert the role to lowercase
        $existingUsername = $this->checkUsernameExists("users_" . $role, $username);
    
        // If username does not exist, proceed to insert values into the respective table
        if (!$existingUsername) {
            $table = "users_" . $role;
            $this->sql = "INSERT INTO  $table (fullname, username, password, email) VALUES ('$fullname ','$username','$password','$email')";
    
            if (mysqli_query($this->connect, $this->sql)) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    //method for getting supplier id
    function getSupplierInfo($username) 
    {
        $username = $this->prepareData($username);
        $this->sql = "SELECT * FROM users_supplier WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row;
        } else {
            return false; // Supplier info not found
        }
    }

    function getFarmerInfo($username) 
    {
        $username = $this->prepareData($username);
        $this->sql = "SELECT * FROM users_farmer WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row;
        } else {
            return false; // Farmer info not found
        }
    }
    
    function addInventory($table, $itemName, $itemType, $itemQuantity, $itemMinQuantity, $itemUnit, $farmerId)
    {
        $itemName = $this->prepareData($itemName);
        $itemType = $this->prepareData($itemType);
        $itemQuantity = $this->prepareData($itemQuantity);
        $itemMinQuantity = $this->prepareData($itemMinQuantity);
        $itemUnit = $this->prepareData($itemUnit);
        $farmerId = $this->prepareData($farmerId);

        $this->sql = "INSERT INTO $table (itemName, itemType, itemQuantity, itemMinQuantity, itemUnit, farmerId) VALUES ('$itemName', '$itemType', '$itemQuantity', '$itemMinQuantity', '$itemUnit', '$farmerId')";

        if(mysqli_query($this->connect, $this->sql)) {
            return true;
        }else return false;
    }

    function displayInventory($table) {
        $this->sql = "SELECT * FROM $table";
        $result = mysqli_query($this->connect, $this->sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $inventory = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                $inventory[] = $row;
            }
    
            //Convert data from the table into a json
            echo json_encode($inventory);
        } else {
            echo json_encode(array());
        }
    }

    function updateItem($table, $itemName, $itemType, $itemQuantity, $itemMinQuantity, $itemUnit, $itemId)
    {
        $itemName = $this->prepareData($itemName);
        $itemType = $this->prepareData($itemType);
        $itemQuantity = $this->prepareData($itemQuantity);
        $itemMinQuantity = $this->prepareData($itemMinQuantity);
        $itemUnit = $this->prepareData($itemUnit);
        $itemId = $this->prepareData($itemId);

        $this->sql = "UPDATE $table SET itemName = ?, itemType = ?, itemQuantity = ?, itemMinQuantity = ?, itemUnit = ? WHERE itemName = ? AND itemId = ?";
        
        $stmt = mysqli_prepare($this->connect, $this->sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $itemName, $itemType, $itemQuantity, $itemMinQuantity, $itemUnit, $itemName, $itemId);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            return false;
        }
    }

    function displaySuppliers($table) {
        $this->sql = "SELECT * FROM $table";
        $result = mysqli_query($this->connect, $this->sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $suppliers = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                $suppliers[] = $row;
            }
    
            //Convert data from the table into a json
            echo json_encode($suppliers);
        } else {
            echo json_encode(array());
        }
    }

    function addPurchase($table, $date, $orderItemName, $orderItemPrice, $orderItemQuantity, $orderItemUnit, $orderTotalPrice, $orderSupplierName, $paymentMethod, $orderCustomerName, $farmerId) {
        $date = date('Y-m-d');
        $currentDate = $this->prepareData($date);
        $orderItemName = $this->prepareData($orderItemName);
        $orderItemPrice = $this->prepareData($orderItemPrice);
        $orderItemQuantity = $this->prepareData($orderItemQuantity);
        $orderItemUnit = $this->prepareData($orderItemUnit);
        $orderTotalPrice = $this->prepareData($orderTotalPrice);
        $orderSupplierName = $this->prepareData($orderSupplierName);
        $paymentMethod = $this->prepareData($paymentMethod);
        $orderCustomerName = $this->prepareData($orderCustomerName);
        $farmerId = $this->prepareData($farmerId);

        $this->sql = "INSERT INTO $table (orderDate, orderItemName, orderItemPrice, orderItemQuantity, orderItemUnit, orderTotalPrice, orderSupplierName, paymentMethod, orderCustomerName, farmerId) VALUES ('$currentDate', '$orderItemName', '$orderItemPrice', '$orderItemQuantity', '$orderItemUnit', '$orderTotalPrice', '$orderSupplierName', '$paymentMethod', '$orderCustomerName', '$farmerId')";

        if(mysqli_query($this->connect, $this->sql)) {
            return true;
        }else return false;
    }

    function viewPurchase($table) {
        $this->sql = "SELECT * FROM $table";
        $result = mysqli_query($this->connect, $this->sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $purchase_order = array();
    
            while ($row = mysqli_fetch_assoc($result)) {
                $purchase_order[] = $row;
            }
    
            //Convert data from the table into a json
            echo json_encode($purchase_order);
        } else {
            echo json_encode(array());
        }
    }

    function purchaseReportData($table, $farmerId) 
    {
        $farmerId = $this->prepareData($farmerId);
        
        $this->sql = "SELECT * FROM $table WHERE farmerId = '$farmerId'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $purchase_order = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $purchase_order[] = $row;
            }

            return $purchase_order;
        } else {
            return array();
        }
    }

    function orderReportData($table, $orderSupplierName) 
    {
        $orderSupplierName = $this->prepareData($orderSupplierName);
        
        $this->sql = "SELECT * FROM $table WHERE orderSupplierName = '$orderSupplierName'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $purchase_order = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $purchase_order[] = $row;
            }

            return $purchase_order;
        } else {
            return array();
        }
    }

    function inventoryReportData($table, $farmerId) 
    {
        $farmerId = $this->prepareData($farmerId);
        
        $this->sql = "SELECT * FROM $table WHERE farmerId = '$farmerId'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $inventory = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $inventory[] = $row;
            }

            return $inventory;
        } else {
            return array();
        }
    }

    function itemReportData($table, $supplierId) 
    {
        $supplierId = $this->prepareData($supplierId);
        
        $this->sql = "SELECT * FROM $table WHERE supplierId = '$supplierId'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $items = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }

            return $items;
        } else {
            return array();
        }
    }


    function addSalesItem($table, $supplierId, $supplierName, $supplierItemName, $supplierItemPrice, $supplierItemDescription) 
    {
        $supplierId = $this->prepareData($supplierId);
        $supplierName = $this->prepareData($supplierName);
        $supplierItemName = $this->prepareData($supplierItemName);
        $supplierItemPrice = $this->prepareData($supplierItemPrice);
        $supplierItemDescription = $this->prepareData($supplierItemDescription);
            
        $this->sql = "INSERT INTO $table (supplierId, supplierName, supplierItemName, supplierItemPrice, supplierItemDescription) VALUES ('$supplierId', '$supplierName', '$supplierItemName', '$supplierItemPrice', '$supplierItemDescription')";
            
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            $errorDescription = mysqli_error($this->connect);
            // Echo the error message for debugging purposes
            echo "Query Error: " . $errorDescription;
            return false;
        }
    }

    function displaySalesItem($table)
    {
        $this->sql = "SELECT * FROM $table";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $supplier_items = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $supplier_items[] = $row;
            }

            // Convert data from the table into a JSON array
            echo json_encode($supplier_items);
        } else {
            echo json_encode(array());
        }
    }

    function updateStatus($table, $orderId, $orderStatus) 
    {
        $orderId = $this->prepareData($orderId);
        $orderStatus = $this->prepareData($orderStatus);

        $this->sql = "UPDATE $table SET orderStatus = '$orderStatus' WHERE orderId = '$orderId'";

        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            return false;
        }
    }


    function cancelPurchase($table, $orderId)
    {
        $orderId = $this->prepareData($orderId);
        $this->sql = "DELETE FROM $table WHERE orderId = '$orderId'";
        
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            return false;
        }
    }

    function deleteSupplierItem($table, $supplierItemId)
    {
        $supplierItemId = $this->prepareData($supplierItemId);
        $this->sql = "DELETE FROM $table WHERE supplierItemId = '$supplierItemId'";
        
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            return false;
        }
    }

    function updateSupplierItem($table, $supplierItemId, $supplierName, $supplierItemName, $supplierItemPrice, $supplierItemDescription)
    {
        $supplierItemId = $this->prepareData($supplierItemId);
        $supplierName = $this->prepareData($supplierName);
        $supplierItemName = $this->prepareData($supplierItemName);
        $supplierItemPrice = $this->prepareData($supplierItemPrice);
        $supplierItemDescription = $this->prepareData($supplierItemDescription);

        $this->sql = "UPDATE $table SET supplierItemName = ?, supplierItemPrice = ?, supplierItemDescription = ? WHERE supplierName = ? AND supplierItemId = ?";
        
        $stmt = mysqli_prepare($this->connect, $this->sql);
        mysqli_stmt_bind_param($stmt, "sssss", $supplierItemName, $supplierItemPrice, $supplierItemDescription, $supplierName, $supplierItemId);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            return false;
        }
    }

    function deleteInventoryItem($table, $itemId)
    {
        $itemId = $this->prepareData($itemId);
        $this->sql = "DELETE FROM $table WHERE itemId = '$itemId'";
        
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            return false;
        }
    }

}

?>