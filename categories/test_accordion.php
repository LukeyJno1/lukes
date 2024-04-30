<?php

//test_accordion.php

require_once './classes/db_connect.php';
require_once './classes/CategoryFormHandler.php';

$pdo = $db->getConnection();
$categoryHandler = new CategoryFormHandler($pdo);

// Retrieve all categories
$categories = $categoryHandler->getAllCategories();

// Function to build the category hierarchy
function buildCategoryHierarchy($categories, $categoryHandler, $parentId = 0) {
    $hierarchy = [];

    foreach ($categories as $category) {
        $categoryId = $category['id'];
        $parentCategoryId = $categoryHandler->getParentCategoryId($categoryId);

        if ($parentCategoryId == $parentId) {
            $children = buildCategoryHierarchy($categories, $categoryHandler, $categoryId);
            $category['children'] = $children;
            $hierarchy[] = $category;
        }
    }

    return $hierarchy;
}

// Build the category hierarchy
$categoryHierarchy = buildCategoryHierarchy($categories, $categoryHandler);

// Function to generate the accordion markup recursively
function generateAccordionMarkup($categories, $level = 0) {
    $markup = '';

    foreach ($categories as $category) {
        $categoryId = $category['id'];
        $categoryName = $category['name'];
        $children = $category['children'];

        $markup .= '<div class="accordion-item">';
        $markup .= '<div class="accordion-header" data-category-id="' . $categoryId . '">';
        $markup .= '<span class="toggle-icon plus-icon"></span>';
        $markup .= '<a href="path/to/category-page.php?categoryId=' . $categoryId . '" class="category-link">' . $categoryName . '</a>';
        $markup .= '</div>';
        
        if (!empty($children)) {
            $markup .= '<div class="accordion-content">';
            $markup .= generateAccordionMarkup($children, $level + 1);
            $markup .= '</div>';
        }
        
        $markup .= '</div>';
    }

    return $markup;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accordion Test Page</title>
    <style>
    /* CSS styles for the accordion */
    .accordion {
        font-family: Arial, sans-serif;
    }
    
    .accordion-item {
        
        border-left: 0px solid #FFA07A; /* Light Salmon color */
        padding: 2px;
        margin-bottom: 2px;
        width: 150px;
    }

    .accordion-header {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 2px;
       
    }
     
    .category-link {
        color: black; /* Change as needed */
        text-decoration: none;
        padding-left: 2px;
    }

    .accordion-header:hover .category-link {
        text-decoration: underline;
    }

    .accordion-content {
        display: none;
        padding-left: 10px;
       
    }

    .toggle-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        cursor: pointer;
        background-size: contain;
        background-repeat: no-repeat;
    }

    .plus-icon {
        background-image: url('http://localhost/listing/assets/images/plus-icon.png');
    }

    .minus-icon {
        background-image: url('http://localhost/listing/assets/images/minus-icon.png');
    }
</style>

</head>
<body>
    <h1>Accordion Test Page</h1>
    <div id="accordion">
        <?php echo generateAccordionMarkup($categoryHierarchy); ?>
    </div>

    <script>
    // JavaScript code for accordion functionality
    document.addEventListener('DOMContentLoaded', function() {
        var accordionIcons = document.querySelectorAll('.toggle-icon');

        accordionIcons.forEach(function(icon) {
            icon.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent the accordion from toggling when the icon is clicked

                // Get the .accordion-content element that is the next sibling of the parent of the icon
                var accordionContent = this.parentNode.nextElementSibling;
                
                // Check if the accordion is currently expanded or not
                var isExpanded = accordionContent.style.display === 'block';
                
                // If expanded, hide it and switch to plus icon, else show it and switch to minus icon
                if (isExpanded) {
                    accordionContent.style.display = 'none';
                    this.classList.remove('minus-icon');
                    this.classList.add('plus-icon');
                } else {
                    accordionContent.style.display = 'block';
                    this.classList.remove('plus-icon');
                    this.classList.add('minus-icon');
                }
            });
        });

        // Category links should only navigate to the category page and not toggle the accordion
        var categoryLinks = document.querySelectorAll('.category-link');
        categoryLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                // No need to stopPropagation here since we want the default link action
            });
        });
    });
</script>

</body>
</html>