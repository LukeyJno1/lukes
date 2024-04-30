<?php
// update_category_relationships.php

// Include necessary files
require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';

// Establish database connection
$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Function to update category relationships
function updateCategoryRelationships($categoryId, $parentIds, $categoryHandler) {
    // Delete existing parent-child relationships for the category
    $categoryHandler->deleteParentChildRelationships($categoryId);

    // Insert new parent-child relationships
    foreach ($parentIds as $parentId) {
        $categoryHandler->insertParentChildRelationship($categoryId, $parentId);
    }

    // Rebuild category closure table
    $categoryHandler->rebuildCategoryClosure();
}

// Function to get all categories with their parent relationships
function getAllCategoriesWithParents($categoryHandler) {
    $categories = $categoryHandler->getAllCategories();
    $categoriesWithParents = [];

    foreach ($categories as $category) {
        $parentCategories = $categoryHandler->getParentCategories($category['id']);
        $categoriesWithParents[] = [
            'id' => $category['id'],
            'name' => $category['name'],
            'parents' => $parentCategories
        ];
    }

    return $categoriesWithParents;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted category ID and parent IDs
    $categoryId = $_POST['category_id'] ?? null;
    $parentIds = $_POST['parent_ids'] ?? [];

    if ($categoryId !== null) {
        // Update the category relationships
        updateCategoryRelationships($categoryId, $parentIds, $categoryHandler);
        echo "Category relationships updated successfully!";
    } else {
        echo "Invalid category ID!";
    }
}

// Get all categories with their parent relationships
$categoriesWithParents = getAllCategoriesWithParents($categoryHandler);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Category Relationships</title>
</head>
<body>
<h2>Current Category Relationships</h2>
<?php if (!empty($categoriesWithParents)): ?>
    <?php
    // Create an array to store top-level categories
    $topLevelCategories = [];

    // Separate top-level categories and their descendants
    foreach ($categoriesWithParents as $category) {
        if (empty($category['parents'])) {
            $topLevelCategories[$category['id']] = $category;
        }
    }

    // Sort top-level categories alphabetically by name
    usort($topLevelCategories, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    // Recursive function to display categories and their descendants
    function displayCategoryTree($category, $categoriesWithParents, $level = 0) {
        $indent = str_repeat('&nbsp;', $level * 4);
        echo "<li>{$indent}Category ID: {$category['id']}, Name: {$category['name']}</li>";

        foreach ($categoriesWithParents as $childCategory) {
            if (in_array($category['id'], array_column($childCategory['parents'], 'id'))) {
                displayCategoryTree($childCategory, $categoriesWithParents, $level + 1);
            }
        }
    }
    ?>

    <ul>
        <?php foreach ($topLevelCategories as $topLevelCategory): ?>
            <?php displayCategoryTree($topLevelCategory, $categoriesWithParents); ?>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No categories found.</p>
<?php endif; ?>

    <h2>Update Category Relationships</h2>
    <form method="post" action="">
        <label for="category_id">Category ID:</label>
        <input type="text" name="category_id" id="category_id" required><br>

        <label for="parent_ids">Parent IDs (comma-separated):</label>
        <input type="text" name="parent_ids" id="parent_ids"><br>

        <input type="submit" value="Update Relationships">
    </form>
</body>
</html>