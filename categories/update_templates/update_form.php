<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Category Pages</title>
</head>
<body>
    <h1>Update Category Pages</h1>
    <form action="update_category_pages.php" method="post">
        <label for="category_pages">Select Category Pages to Update:</label><br>
        <select name="category_pages[]" id="category_pages" multiple size="10">
            <?php
            require 'path/to/your/functions.php'; // Ensure this path is correct

            $categoryPagesDir = __DIR__ . '/../category_pages/';
            $categoryPages = glob($categoryPagesDir . '*.php');
            foreach ($categoryPages as $page) {
                $pageName = basename($page);
                $categoryId = getCategoryID($pageName);
                echo '<option value="' . htmlspecialchars($pageName) . '|' . $categoryId . '">' . htmlspecialchars($pageName) . '</option>';
                error_log("Processing page: " . $pageName . " with Category ID: " . $categoryId);
            }
            ?>
        </select><br><br>
        <input type="submit" value="Update Selected Pages">
    </form>
</body>
</html>
