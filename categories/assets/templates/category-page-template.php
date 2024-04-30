<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "/categories/";

require_once $rootPath . "classes/db_connect.php";
$pdo = $db->getConnection();

require_once $rootPath . "classes/CategoryFormHandler.php";
// Ensure $categoryPagesDir is defined. Adjust the path as necessary.
$categoryPagesDir = '/category_pages/';

// Assuming $db is already instantiated and available
$pdo = $db->getConnection();

// Correct instantiation with both parameters
$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo); // Only pass the $pdo argument

require_once $rootPath . "assets/breadcrumb_utils.php";

// Define the path to accordion utilities
$accordionUtilsPath = $rootPath . "assets/accordion_utils.php";
if (file_exists($accordionUtilsPath)) {
    require_once $accordionUtilsPath;
} else {
    error_log("Failed to include accordion_utils.php: File does not exist.");
    // Handle the error appropriately
}
//var_dump($_GET);
//var_dump($_POST);
//exit;  // Temporarily halt the script to read the output

// Check for category ID
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $categoryId = $_POST['id'];
} else {
    echo "Category ID is missing.";
    exit; // Stop further execution
}

require_once $rootPath . "assets/accordion_utils.php";

if (file_exists($accordionUtilsPath)) {
    require_once $accordionUtilsPath;
} else {
    error_log("Failed to include accordion_utils.php: File does not exist.");
    // Handle the error appropriately
}
function displayChildren($children) {
    echo '<div class="children">';
    foreach ($children as $child) {
        echo "<div>{$child['name']}</div>";
        if (!empty($child['children'])) {
            displayChildren($child['children']);  // Recursive call
        }
    }
    echo '</div>';
}

if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];
} elseif (isset($_POST['id'])) {
    // If using POST instead
    $categoryId = $_POST['id'];
} else {
    // ID is not found, handle the error
    echo "Category ID is missing.";
    exit; // Stop further execution
}

// Fetch the current category slug from the URL or routing mechanism
$currentCategorySlug = $_GET['category_slug'] ?? 'default-slug';

// Validate the category slug and redirect if necessary
if ($currentCategorySlug === 'default-slug' || !preg_match('/^[a-zA-Z0-9-]+$/', $currentCategorySlug)) {
    error_log("Invalid or default slug used: " . $currentCategorySlug);
    header("Location: /error-page.php");
    exit;
}

// Fetch category details by slug
$categoryDetails = $categoryFormHandler->getCategoryBySlug($currentCategorySlug);
if (!$categoryDetails) {
    error_log("Category not found with slug: $currentCategorySlug");
    header("Location: /error-page.php");
    exit;
}

$currentCategoryId = $categoryDetails['id'];
error_log('Category ID passed: ' . $categoryId);

// Debug: Output current Category ID
echo "Current Category ID: " . (isset($_GET['category_id']) ? $_GET['category_id'] : 'None') . "<br>";


$categoryFormHandler->displayNestedCategories($nestedCategories);
$nestedCategories = $categoryFormHandler->getNestedCategories($currentCategoryId);

$categoryName = $categoryDetails['name'] ?? 'Default Category Name';
$keywords = $categoryDetails['keywords'] ?? 'default, keywords';

// Fetch descendants of the current category and build hierarchical structure
$descendantCategories = $categoryFormHandler->getDescendants($currentCategoryId);
error_log('Descendant Categories: ' . print_r($descendantCategories, true));


$nestedCategories = $categoryFormHandler->buildHierarchy($descendantCategories, $currentCategoryId);
error_log('Nested Categories: ' . print_r($nestedCategories, true));
// Check if $nestedCategories is valid before processing
if (is_array($nestedCategories) && !empty($nestedCategories)) {
    foreach ($nestedCategories as $category) {
        echo "<div>{$category['name']}</div>"; // Example of output
        if (!empty($category['children'])) {
            // Recursive function or loop to handle children
            displayChildren($category['children']);
        }
    }
} else {
    error_log('No categories to process or $nestedCategories is not an array.');
    echo "<p>No categories available.</p>";
}

$accordionHTML = generateAccordionMarkup($nestedCategories);
error_log('Accordion HTML: ' . $accordionHTML);

// Generate breadcrumbs HTML
$breadcrumbsHTML = generateBreadcrumbsHTML($currentCategoryId, $categoryFormHandler);

$categoryId = $_GET['id'] ?? null;
if (!$categoryId) {
    echo "Category ID is missing.";
    exit;
}


header("Location: category-page-template.php?id=" . $categoryId);
error_log("Received category ID: " . $categoryId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category: <?php echo htmlspecialchars($categoryName); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($categoryDetails['description'] ?? 'General category information'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <link rel="stylesheet" href="/categories/assets/css/style.css">
</head>
<body>
    <h1>Category: <?php echo htmlspecialchars($categoryName); ?></h1>
    <ul class="breadcrumb-trail"><?php echo $breadcrumbsHTML; ?></ul>
    <div class="side-area">
        <h2>Descendants:</h2>
        <div class="accordion"><?php echo $accordionHTML; ?></div>
    </div>
    <main>
        <h2>Category Content</h2>
        <!-- Placeholder for dynamic category-specific content -->
    </main>
    <footer><p>Footer content goes here.</p></footer>
    <script src="/categories/assets/js/category-page.js"></script>
</body>
</html>
