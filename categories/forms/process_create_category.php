<?php

// process_create_category.php
error_log('process_create_category.php executed');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_error.log');

require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';
$pdo = $db->getConnection(); // Get the PDO object
$categoryHandler = new CategoryFormHandler($pdo);

$name = $_POST['name'] ?? null;
if ($name === null) {
    error_log('Category name is required.');
    header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Category name is required.'));
    exit;
}
$description = $_POST['description'] ?? '';
$parentIDs = $_POST['parent_ids'] ?? [];
$existingPage = $_POST['existing_page'] ?? null;
$categoryPagesDir = $_SERVER['DOCUMENT_ROOT'] . '/categories/category_pages/';

if ($categoryHandler->doesCategoryExistByName($name)) {
    error_log('Category name already exists: ' . $name);
    header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Category name already exists.'));
    exit;
}

if (!empty($parentIDs)) {
    foreach ($parentIDs as $parentID) {
        if (!$categoryHandler->checkParent($parentID)) {
            error_log('One or more selected parent categories do not exist.');
            header('Location: /categories/forms/create-category-form.php?error=' . urlencode('One or more selected parent categories do not exist.'));
            exit;
        }
    }
}

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/categories/category_images/';
    $imageFileName = basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . $imageFileName;

    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        error_log('Failed to create the category images directory.');
        header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Failed to create the category images directory.'));
        exit;
    }

    try {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
            }
            if ($_FILES["image"]["size"] > 500000) {
                throw new Exception('Your Image has to be a maximum of 5mb in size.');
            }
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                throw new Exception('There was an error uploading your file.');
            }
            $imagePath = '/categories/category_images/' . $imageFileName;
        } else {
            throw new Exception('File is not an image.');
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        header('Location: /categories/forms/create-category-form.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

$templatePath = __DIR__ . '/../assets/templates/category-page-template.php';
if (!file_exists($templatePath)) {
    error_log("Template file does not exist: " . $templatePath);
    header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Template file does not exist.'));
    exit;
}

$keywords = $_POST['keywords'] ?? '';
$keywordsArray = explode(',', $keywords);
$cleanedKeywords = array_map('trim', $keywordsArray);
$keywordsString = implode(',', $cleanedKeywords);

try {
    $newCategoryId = $categoryHandler->addCategory($name, $description, $imagePath, $keywordsString, null, $parentIDs);
    if (!$newCategoryId) {
        throw new Exception("Failed to obtain a valid category ID.");
    }

    // Log the new category ID for debugging purposes
    error_log("New category created with ID: " . $newCategoryId);

    // Proceed with file operations
    $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($name));
    $newFileName = $existingPage ? $existingPage . '.php' : "{$sanitizedName}.php";
    $fullPath = $categoryPagesDir . $newFileName;

    if (!file_exists($fullPath)) {
        $templateContent = file_get_contents($templatePath);
        $templateContent = str_replace('{$categoryId}', $newCategoryId, $templateContent);
        $templateContent = str_replace('{$categoryName}', $name, $templateContent);
        if (file_put_contents($fullPath, $templateContent) === false) {
            throw new Exception("Failed to write to the file: " . $fullPath);
        }
    } else {
        $existingPageContent = file_get_contents($fullPath);
        $existingPageContent = str_replace('{$categoryId}', $newCategoryId, $existingPageContent);
        $existingPageContent = str_replace('{$categoryName}', $name, $existingPageContent);
        if (file_put_contents($fullPath, $existingPageContent) === false) {
            throw new Exception("Failed to update the existing file: " . $fullPath);
        }
    }

    // Redirect to the success page
    header('Location: /categories/forms/success_page.php?categoryId=' . urlencode($newCategoryId) . '&newPageSlug=' . urlencode($sanitizedName));
    exit;

} catch (Exception $e) {
    error_log("Error creating/updating category: " . $e->getMessage());
    header('Location: /categories/forms/create-category-form.php?error=' . urlencode($e->getMessage()));
    exit;
}
