<?php
// Include necessary files and establish database connection
require_once __DIR__ . '/classes/db_connect.php';
require_once __DIR__ . '/classes/CategoryFormHandler.php';

$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Get the current category ID from the URL parameter
$currentCategoryId = $_GET['category_id'] ?? null;

// Get the current category details
$currentCategory = $categoryHandler->getCategoryById($currentCategoryId);

// Check if the category exists
if ($currentCategory === null) {
    // Handle case when category is not found
    echo "Category not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<h1>Category: <?= htmlspecialchars($currentCategory['name'] ?? '') ?></h1>
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>
<body>
    <h1>Category: <?= htmlspecialchars($currentCategory['name']) ?></h1>

    <!-- Breadcrumbs display logic -->
    <nav class="breadcrumbs">
        <?php include __DIR__ . '/assets/breadcrumbs.php'; ?>
    </nav>

    <!-- Accordion for descendants -->
    <div class="side-area">
        <h2>Descendants:</h2>
        <div class="accordion">
            <?php include __DIR__ . '/assets/accordion_list.php'; ?>
        </div>
    </div>

    <script src="/assets/js/script.js"></script>
</body>
</html>