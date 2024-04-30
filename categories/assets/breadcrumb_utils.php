<?php

// assets/breadcrumb_utils.php

$rootPath = $_SERVER['DOCUMENT_ROOT'] . "/listing/";
require_once $rootPath . "classes/CategoryFormHandler.php";

function generateBreadcrumbsHTML($categoryId, CategoryFormHandler $categoryHandler) {
    $breadcrumbs = $categoryHandler->getBreadcrumbTrail($categoryId);
    $breadcrumbs = array_reverse($breadcrumbs);  // Reverse to display from Home to current
    $breadcrumbsHTML = '<ul class="breadcrumb-trail"><li><a href="/">Home</a></li>'; // Start with Home
    foreach ($breadcrumbs as $category) {
        $breadcrumbsHTML .= '<li><a href="/category/' . htmlspecialchars($category['slug']) . '">' . htmlspecialchars($category['name']) . '</a></li>';
    }
    $breadcrumbsHTML .= '</ul>';
    return $breadcrumbsHTML;
}

