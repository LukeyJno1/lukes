<?php
// Include the necessary files (adjust the paths as needed)
require_once __DIR__ . '/classes/CategoryFormHandler.php';
require_once __DIR__ . '/get_category_hierarchy.php';

// Get the categories from the get_category_hierarchy.php file
$categories = require_once __DIR__ . '/get_category_hierarchy.php';

// Return the categories as JSON
header('Content-Type: application/json');
echo json_encode($categories);

// Create an instance of the Database class
$db = new Database($host, $db_name, $username, $password);
$pdo = $db->getConnection();

// Create an instance of the CategoryFormHandler class
$categoryHandler = new CategoryFormHandler($pdo);

// Function to recursively get descendants of a category
function getDescendantsRecursive($categoryId, $categoryHandler) {
    $descendants = $categoryHandler->getDescendants($categoryId);
    foreach ($descendants as &$descendant) {
        $descendant['descendants'] = getDescendantsRecursive($descendant['id'], $categoryHandler);
    }
    return $descendants;
}

// Get all top-level categories
$categories = $categoryHandler->getAllCategories();

// Add descendants to each category
foreach ($categories as &$category) {
    $category['descendants'] = getDescendantsRecursive($category['id'], $categoryHandler);
}

// Return the categories as JSON
header('Content-Type: application/json');
echo json_encode($categories);