<?php
require_once __DIR__ . '/../classes/CategoryFormHandler.php'; // Correct the path as necessary

$categoryHandler = new CategoryFormHandler();

try {
    // Use a unique name to avoid unique constraint violations.
    $newCategoryId = $categoryHandler->addCategory('Unique Test Category', 'Description for unique test category', null, null, []);
    echo "New category ID: " . $newCategoryId;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
