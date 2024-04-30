<?php
// success_page.php
$categoryId = $_GET['categoryId'] ?? null;
$newPageSlug = $_GET['newPageSlug'] ?? null; // Receive the slug from the query parameter

if ($newPageSlug) {
    $categoryPageLink = "/categories/category_pages/{$newPageSlug}.php"; // Use the slug for the link
} else {
    $categoryPageLink = null; // No link if no slug is provided
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Creation Success</title>
</head>
<body>
    <?php if ($categoryPageLink): ?>
        <p>Category created successfully! You can view the category <a href="<?= htmlspecialchars($categoryPageLink); ?>">here</a>.</p>
    <?php else: ?>
        <p>Category creation was successful, but no link can be provided.</p>
    <?php endif; ?>
    <p><a href="/categories/forms/create-category-form.php">Create another category</a></p>
</body>
</html>
