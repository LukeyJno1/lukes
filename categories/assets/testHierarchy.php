
<?php
require_once 'path/to/your/autoload.php'; // Adjust the path as necessary

// Assuming $categoryHandler is an instance of the class that contains buildHierarchy
$categoryHandler = new CategoryHandler(); // Replace with actual instantiation if needed

$testData = [
    ['id' => 1, 'name' => 'Parent', 'slug' => 'parent', 'parent_id' => 0],
    ['id' => 2, 'name' => 'Child', 'slug' => 'child', 'parent_id' => 1]
];
$nestedCategories = $categoryHandler->buildHierarchy($testData);
error_log('Test Nested Categories: ' . print_r($nestedCategories, true));

// Optionally, print the results to the browser for quick viewing
echo '<pre>' . print_r($nestedCategories, true) . '</pre>';
