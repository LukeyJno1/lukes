<?php
// process_category_change.php
require 'classes/db_connect.php';
require 'classes/CategoryFormHandler.php';

$formHandler = new CategoryFormHandler();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the category ID
    $category_id = $_POST['category_id'];

    // Get a valid database connection
    $conn = db_connect(); // This function needs to return a valid PDO or mysqli connection

    // Check if a new name has been submitted and is not empty
    if (!empty($_POST['new_name'])) {
        $formHandler->renameCategory($conn, $category_id, $_POST['new_name']);
    }

    // Check if the category should be deleted
    if (isset($_POST['delete_category'])) {
        $options = [
            // Populate this array based on other form inputs, e.g., 'delete_descendants'
        ];
        $formHandler->deleteCategory($conn, $category_id, $options);
    }

    // Close the connection
    $conn = null; // If using PDO, for mysqli use $conn->close();

    // Redirect to success page or handle errors
    header('Location: success_page.php');
    exit();
}
?>
