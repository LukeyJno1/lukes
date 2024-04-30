<?php
// file_tree.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function generateFileTree($directory) {
    echo "Scanning directory: $directory<br>";
    $tree = [];
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $fullPath = $directory . '/' . $file;
            if (is_dir($fullPath)) {
                $tree[] = [
                    'text' => $file,
                    'children' => generateFileTree($fullPath) // Recursively generate tree for subdirectories
                ];
            } else {
                $tree[] = [
                    'text' => $file
                ];
            }
        }
    }
    return $tree;
}

header('Content-Type: application/json');
echo json_encode(generateFileTree('.'));
?>