<?php

//get_category_details_ajax.php

// Path to the classes directory from the ajax directory
require_once __DIR__ . '/../classes/CategoryFormHandler.php';
require_once __DIR__ . '/../classes/db_connect.php';

$db = new mysqli($servername, $username, $password, $dbname);
$pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

$categoryHandler = new CategoryFormHandler($pdo);

$categoryId = $_POST['categoryId'] ?? null;

$response = [];

if ($categoryId) {
    try {
        $categoryDetails = $categoryHandler->getCategoryById($categoryId);
        $response = [
            'status' => 'success',
            'categoryDetails' => $categoryDetails
        ];
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Category ID is required.'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
