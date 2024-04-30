<?php
// This file would be saved as getDescendants.php in your /listing/api directory
require_once $_SERVER['DOCUMENT_ROOT'] . "/listing/classes/db_connect.php";
$pdo = $db->getConnection();
require_once $_SERVER['DOCUMENT_ROOT'] . "/listing/classes/CategoryFormHandler.php";
$categoryHandler = new CategoryFormHandler($pdo);
require_once $_SERVER['DOCUMENT_ROOT'] . "/listing/assets/accordion_utils.php";


$categoryID = $_GET['categoryID'] ?? null;
if (!$categoryID) {
    echo json_encode(['success' => false]);
    exit;
}

$descendants = $categoryHandler->getDescendants($categoryID);
$hierarchicalDescendants = $categoryHandler->buildHierarchy($descendants);
$accordionHTML = generateAccordionMarkup($hierarchicalDescendants);

echo json_encode(['success' => true, 'accordionHTML' => $accordionHTML]);
?>
