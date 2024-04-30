<?php
//category_functions.php

function getCategoryDetails($categoryHandler, $categoryId) {
    $categoryDetails = $categoryHandler->getCategoryById($categoryId);
    return $categoryDetails;
}

function findCategory($categories, $categoryId)
{
    foreach ($categories as $category) {
        // Debug: Print the current category being checked
        //echo "Checking category: " . $category['name'] . " (ID: " . $category['id'] . ")<br>";

        if ($category['id'] == $categoryId) {
            // Debug: Print the found category
            echo "Found category: " . $category['name'] . " (ID: " . $category['id'] . ")<br>";
            return $category;
        }
        if (!empty($category['descendants'])) {
            $foundCategory = findCategory($category['descendants'], $categoryId);
            if ($foundCategory !== null) {
                return $foundCategory;
            }
        }
    }
    // Debug: Print if no category is found
    //echo "Category not found<br>";
    return null;
}

function getCategories($pdo)
{
    $query = "SELECT * FROM categories";
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Print the retrieved categories
    echo "Retrieved categories:<br>";
    echo "<pre>";
    // print_r($categories);
    echo "</pre>";
    
    return $categories;
}
// Function to build the category hierarchy
function buildCategoryHierarchy($categories, $categoryHandler, $parentId = 0) {
    $hierarchy = [];

    foreach ($categories as $category) {
        $categoryId = $category['id'];
        $parentCategoryId = $categoryHandler->getParentCategoryId($categoryId);

        if ($parentCategoryId == $parentId) {
            // Build the hierarchy for children using recursion
            $children = buildCategoryHierarchy($categories, $categoryHandler, $categoryId);
            if ($children) {
                // The children are stored in both 'children' and 'descendants' for compatibility
                $category['children'] = $children;
                $category['descendants'] = $children;
            }
            // Store the category in the hierarchy
            $hierarchy[$categoryId] = $category;
        }
    }

    return $hierarchy;
}

function displayDescendants($descendants, $level = 0) {
    if (!empty($descendants)) {
        echo '<ul class="descendants-list level-' . $level . '">';

        foreach ($descendants as $descendant) {
            // Only echo out an li if there is a name for the descendant
            if (isset($descendant['name']) && $descendant['name'] !== '') {
                $hasChildren = !empty($descendant['descendants']);
                echo '<li class="descendant-item">';
                echo '<div class="accordion-header">';
                if ($hasChildren) {
                    // Only add toggle span if there are children
                    echo '<span class="toggle-icon"></span>';
                }
                echo htmlspecialchars($descendant['name']);
                echo '</div>';

                if ($hasChildren) {
                    // Recursively call displayDescendants for the child categories
                    echo '<div class="accordion-content" style="display: none;">';
                    displayDescendants($descendant['descendants'], $level + 1);
                    echo '</div>'; // End of accordion content for this level
                }
                echo '</li>'; // End of list item for this category
            }
        }
        echo '</ul>'; // End of list for this level of descendants
    }
}
