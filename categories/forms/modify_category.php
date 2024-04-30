<?php
// Include the database connection and the CategoryFormHandler class
require '../classes/db_connect.php'; // Adjust the path if needed
require '../classes/CategoryFormHandler.php'; // Adjust the path if needed

// Assuming db_connect.php returns a PDO connection
$dbConnection = db_connect();

// Create an instance of CategoryFormHandler with the database connection
$formHandler = new CategoryFormHandler($dbConnection);

// Get all categories using the CategoryFormHandler instance
$categories = $formHandler->getAllCategories();

// If you have a method to get the current category, make sure to include the file where the method is defined.
// Otherwise, remove this line or ensure you define $currentCategory somewhere above this.
// $currentCategory = getCurrentCategory(); // Replace with actual method to get the current category

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Category</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Category: <?= isset($currentCategory['name']) ? htmlspecialchars($currentCategory['name']) : 'Select Category' ?></h1>

<form action="../classes/process_category_change.php" method="post" enctype="multipart/form-data">
    <!-- Category Selection -->
    <select name="category_id" id="categorySelect" onchange="updateFormOptions(this.value)">
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['id']) ?>">
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
  
    <!-- Modification and Deletion Options here -->

    <input type="submit" value="Submit">
</form>

<script src="../js/modify-category.js"></script>

</body>
</html>
