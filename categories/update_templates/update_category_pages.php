<?php
// update_category_pages.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../classes/db_connect.php';
    require_once __DIR__ . '/../classes/CategoryFormHandler.php';

    // Assuming $db is already instantiated and available
    $pdo = $db->getConnection();
    $categoryPagesDir = '/category_pages/'; // Define the path to category pages directory
    $categoryHandler = new CategoryFormHandler($pdo, $categoryPagesDir);

// Assuming you have a method to get the list of category pages
$categoryPages = $categoryHandler->getListOfCategoryPages();
    $selectedPages = $_POST['category_pages'] ?? [];
    $templateFile = __DIR__ . '/../assets/templates/category-page-template.php';

    foreach ($selectedPages as $selectedPage) {
        $parts = explode('|', $selectedPage);
        if (count($parts) < 2) {
            error_log("Invalid page selection: " . $selectedPage);
            continue; // Skip this iteration if the category ID is missing
        }
        list($pageName, $categoryId) = $parts;
        $sanitizedFileName = basename($pageName, '.php') . '.php';
        $pagePath = $categoryPagesDir . $sanitizedFileName;

        $categoryId = $categoryHandler->getCategoryIdBySlug($sanitizedFileName);
        if ($categoryId === null) {
            error_log("Category ID not found for page: " . $sanitizedFileName);
            continue; // Skip to the next page if the category is not found
        }

        if (file_exists($pagePath)) {
            if (!copy($templateFile, $pagePath)) {
                error_log("Failed to update the file: " . $pagePath);
            } else {
                error_log("Updated the file successfully: " . $pagePath);
                // Optionally redirect to a category-specific page or log success
            }
        } else {
            error_log("Page not found: " . $pagePath);
        }
    }
    echo 'Selected category pages have been updated successfully.';
} else {
    echo 'Invalid request method.';
}
?>