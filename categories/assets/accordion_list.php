<?php
// accordion_list.php

// Debugging statement
//var_dump($currentCategoryId);

// Include necessary files and establish database connection
require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';


$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Function to display the accordion list recursively
function displayAccordionList($categoryId, $categoryHandler) {
    $descendants = $categoryHandler->getDescendants($categoryId);
    
    if (!empty($descendants)) {
        echo '<ul>';
        foreach ($descendants as $descendant) {
            echo '<li>';
            echo '<div class="accordion-header">' . htmlspecialchars($descendant['name']) . '</div>';
            echo '<div class="accordion-content">';
            displayAccordionList($descendant['id'], $categoryHandler);
            echo '</div>';
            echo '</li>';
        }
        echo '</ul>';
    }
}
?>

<div class="accordion">
    <?php displayAccordionList($currentCategoryId, $categoryHandler); ?>
</div>