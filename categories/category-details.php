<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';

$currentCategoryId = $_GET['categoryId'] ?? null;
if ($currentCategoryId === null) {
    die("Category ID not provided.");
}

$currentCategory = $categoryHandler->getCategoryById($currentCategoryId);
if ($currentCategory === null) {
    die("Category not found.");
}

$breadcrumbs = buildBreadcrumbs($currentCategory, $categoryHandler);
$descendantsContent = displayCategoryAccordion($currentCategoryId, $pdo);

// Load the template
$templateFilePath = __DIR__ . '/../assets/templates/category-page-template.php';
if (!file_exists($templateFilePath)) {
    die("Template file not found.");
}

ob_start(); // Start output buffering
include $templateFilePath; // Include the PHP template file
$templateContent = ob_get_clean(); // Get the output and clean the buffer

// Replace placeholders
$updatedContent = str_replace('{{categoryId}}', $currentCategoryId, $templateContent);
$updatedContent = str_replace('{{categoryName}}', htmlspecialchars($currentCategory['name']), $updatedContent);
$updatedContent = str_replace('{{keywords}}', htmlspecialchars($currentCategory['keywords'] ?? ''), $updatedContent);
$updatedContent = str_replace('{{breadcrumbsHTML}}', generateBreadcrumbsHTML($breadcrumbs), $updatedContent);
$updatedContent = str_replace('{{accordionHTML}}', $descendantsContent, $updatedContent);

echo $updatedContent; // Output the final content

function generateBreadcrumbsHTML($breadcrumbs) {
    $html = '<ul>';
    foreach ($breadcrumbs as $breadcrumb) {
        $html .= '<li><a href="/path/to/category/' . $breadcrumb['id'] . '">' . htmlspecialchars($breadcrumb['name']) . '</a></li>';
    }
    $html .= '</ul>';
    return $html;
}

function displayCategoryAccordion($categoryId, $pdo) {
    // implementation goes here
    return "<div>Accordion Content for Category ID: $categoryId</div>";
}

function generateAccordionMarkup($categories, $level = 0) {
    $markup = '';
    foreach ($categories as $category) {
        $children = $category['children'] ?? [];
        $markup .= "<div class=\"accordion-item\">";
        $markup .= "<div class=\"accordion-header\" data-category-id=\"{$category['id']}\">";
        $markup .= "<span class=\"toggle-icon plus-icon\"></span>";
        $markup .= "<a href=\"/categories/category_pages/{$category['slug']}.php\" class=\"category-link\">" . htmlspecialchars($category['name']) . "</a>";
        $markup .= "</div>";
        if ($children) {
            $markup .= "<div class=\"accordion-content\" style=\"display: none;\">";
            $markup .= generateAccordionMarkup($children, $level + 1);
            $markup .= "</div>";
        }
        $markup .= "</div>";
    }
    return $markup;
}
