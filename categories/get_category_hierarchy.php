<?php
// get_category_hierarchy.php

require_once __DIR__ . '/classes/db_connect.php';
require_once __DIR__ . '/category_functions.php';
require_once __DIR__ . '/classes/CategoryFormHandler.php';

// Use the Database class to establish a PDO connection
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

// Get all categories
$categories = $categoryHandler->getAllCategories();

// Build a full category hierarchy
$categoryHierarchy = [];
foreach ($categories as $category) {
    if (empty($category['parent_id'])) { // Assuming 'parent_id' is the column for the parent category ID
        // This category is a top-level category
        $category['descendants'] = getDescendantsRecursive($category['id'], $categoryHandler);
        $categoryHierarchy[$category['id']] = $category;
    }
}

// Return the full category hierarchy
return $categoryHierarchy;
