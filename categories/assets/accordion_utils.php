<?php
function generateAccordionMarkup($categories, $level = 0) {
    $markup = '<div class="accordion-level-' . $level . '">'; // Start accordion level div

    foreach ($categories as $category) {
        // Check if essential data is present
        if (!isset($category['id']) || !isset($category['name']) || !isset($category['slug'])) {
            error_log('Missing essential category data: ' . print_r($category, true));
            continue; // Skip this iteration if essential data is missing
        }

        // Start accordion item
        $markup .= '<div class="accordion-item">';

        // Accordion header
        $markup .= '<div class="accordion-header" data-category-id="' . $category['id'] . '" aria-expanded="false">';
        $markup .= '<span class="toggle-icon plus-icon" onclick="toggleAccordion(this);"></span>';
        $markup .= '<a href="/category/' . $category['slug'] . '" class="category-link">' . htmlspecialchars($category['name']) . '</a>';
        $markup .= '</div>'; // Close accordion-header div

        // Accordion content (for children)
        if (!empty($category['children'])) {
            $markup .= '<div class="accordion-content" style="display: none;">';
            $markup .= generateAccordionMarkup($category['children'], $level + 1); // Recursive call for children
            $markup .= '</div>'; // Close accordion-content div
        }

        $markup .= '</div>'; // Close accordion-item div
    }

    $markup .= '</div>'; // Close accordion-level div
    return $markup;
}

// Example usage
$categories = [
    // Your categories array
];

echo generateAccordionMarkup($categories);
?>
