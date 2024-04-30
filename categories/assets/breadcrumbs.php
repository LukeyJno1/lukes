<?php
// breadcrumbs.php
// Debugging statement
var_dump($currentCategoryId);
// Include necessary files and establish database connection
require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';

$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Function to get the breadcrumb trail for a category
function getBreadcrumbTrail($categoryId, $categoryHandler)
{
    $breadcrumbs = [];

    while ($categoryId !== null) {
        $category = $categoryHandler->getCategoryById($categoryId);
        if ($category !== null) {
            $breadcrumbs[] = $category;

            // Get the parent category ID from the category_parents table
            $parentId = $categoryHandler->getParentCategoryId($categoryId);
            $categoryId = $parentId;
        }

        break;
    }

    return array_reverse($breadcrumbs);
}


// Get the breadcrumb trail for the current category
$breadcrumbTrail = getBreadcrumbTrail($currentCategoryId, $categoryHandler);
?>

<nav class="breadcrumbs">
    <?php foreach ($breadcrumbTrail as $index => $breadcrumb): ?>
        <?php if ($index === count($breadcrumbTrail) - 1): ?>
            <span><?= htmlspecialchars($breadcrumb['name']) ?></span>
        <?php else: ?>
            <a href="/category_pages/<?= $breadcrumb['slug'] ?>.php"><?= htmlspecialchars($breadcrumb['name']) ?></a> &gt;
        <?php endif; ?>
    <?php endforeach; ?>
</nav>