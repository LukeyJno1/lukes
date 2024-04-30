<?php
//create-category-form.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';
$pdo = $db->getConnection(); // Get the PDO connection from your Database class instance

if ($pdo === null) {
    error_log("Failed to obtain PDO connection object.");
    die("Failed to connect to the database. Please try again later.");
}
error_log("PDO connection object obtained successfully.");

$categoryHandler = new CategoryFormHandler($pdo); // Now you pass the PDO object, not the individual connection parameters

// Assuming $name is retrieved from POST data or another source
$name = $_POST['name'] ?? '';

// Use the $categoryHandler object to call the method
if ($categoryHandler->doesCategoryExistByName($name)) {
    error_log('Category name already exists.');
    header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Category name already exists.'));
    exit;
}

// ... rest of the code ...

// Pass the PDO object to CategoryFormHandler
$categoryHandler = new CategoryFormHandler($pdo); // Pass the PDO object here
$categories = $categoryHandler->getAllCategories();
$categoryPagesDir = $_SERVER['DOCUMENT_ROOT'] . '/categories/category_pages/';
$existingCategoryPages = glob($categoryPagesDir . '*.php');
$existingCategoryPageNames = array_map(function ($page) {
    return basename($page, '.php');
}, $existingCategoryPages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Category</title>
    <link rel="stylesheet" href="/categories/assets/css/style.css">
</head>
<body>
<form id="create-category-form" action="/categories/ajax/create_category_ajax.php" method="POST" enctype="multipart/form-data">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <label for="keywords">Keywords (comma-separated):</label>
        <input type="text" id="keywords" name="keywords">
        
        <label for="parent-category">Parent Category:</label>
        <select name="parent_ids[]" id="parent-category" multiple>
            <option value="">No Parent Category</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php } ?>
        </select>

        <label for="existing-page">Category Page:</label>
        <select name="existing_page" id="existing-page">
            <option value="">Create New Page</option>
            <?php foreach ($existingCategoryPageNames as $pageName): ?>
                <option value="<?= htmlspecialchars($pageName) ?>"><?= htmlspecialchars($pageName) ?></option>
            <?php endforeach; ?>
        </select>

        <p>If you do not select an existing page, a new page will be created automatically for your category upon submission.</p>
        <input type="submit" value="Create Category">
    </form>

    <script 
    
    src="/categories/assets/js/ajax-category-creation.js"></script>
</body>
</html>