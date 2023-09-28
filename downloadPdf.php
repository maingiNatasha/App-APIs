<?php

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    $file = 'C:\\xampp\\htdocs\\PrototypeApi\\' . $filename;

    if (file_exists($file)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($file);
        exit;
    } else {
        echo "Error: File not found.";
    }
} else {
    echo "Error: 'filename' parameter is missing in the request.";
}
?>