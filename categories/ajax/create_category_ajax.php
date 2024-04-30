<?php
//create_category_ajax.php
require_once __DIR__ . '/../classes/CategoryFormHandler.php';
require_once __DIR__ . '/../classes/db_connect.php';

// Assuming $pdo is obtained from db_connect.php
$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Initialize the response array
$response = [];

// Grab the POST data
$categoryName = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$parentIds = $_POST['parent_ids'] ?? []; // Assuming this is an array of parent category IDs
$image = $_FILES['image'] ?? null; // Assuming an image upload, handle this accordingly
$keywords = $_POST['keywords'] ?? '';

try {
    if ($image) {
        // Process image upload here and get path
        $imagePath = '/category_images/'; // This should be the result of your image processing script
    } else {
        $imagePath = ''; // Default image path or handling
    }

    // Now that all variables are defined, call addCategory
    $categoryId = $categoryHandler->addCategory($categoryName, $description, $imagePath, $keywords, $parentIds);
    
    // After successfully adding the category, retrieve the slug
    // This assumes that your addCategory method or another method sets or returns the slug
    $slug = $categoryHandler->generateSlug($categoryName); // Generate the slug for the response

    $response['status'] = 'success';
    $response['message'] = "Category created successfully.";
    $response['categoryId'] = $categoryId;
    $response['slug'] = $slug; // Now $slug is defined

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = "Error creating category: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);