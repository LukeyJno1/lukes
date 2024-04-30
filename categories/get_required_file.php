<?php
// get_required_file.php

// Get the selected file from the AJAX request
$selectedFile = isset($_POST['file']) ? $_POST['file'] : '';
echo "Selected File: " . htmlspecialchars($selectedFile) . "<br>";

if (!empty($selectedFile)) {
    // Use the 'id' property as the file path
    $filePath = './' . ltrim($selectedFile, '/');
    // Ensure the file path starts with './'
    echo "Full File Path: " . htmlspecialchars($filePath) . "<br>";

    if (file_exists($filePath)) {
        echo "File exists.<br>";

        // Read the file content
        $fileContent = file_get_contents($filePath);

        // Process required files and output them
        $requiredFiles = [];
        $lines = explode("\n", $fileContent);
        foreach ($lines as $line) {
            // Check for PHP includes
            if (preg_match('/\b(?:include|require(?:_once)?)\s*\(\s*[\'"]([^\'"]+)[\'"]\)\s*;/', $line, $matches)) {
                $requiredFiles[] = $matches[1];
            }
            // Check for linked CSS files
            elseif (preg_match('/<link\s+[^>]*href\s*=\s*[\'"]([^\'"]+)[\'"]/i', $line, $matches)) {
                $requiredFiles[] = $matches[1];
            }
            // Check for linked JavaScript files
            elseif (preg_match('/<script\s+[^>]*src\s*=\s*[\'"]([^\'"]+)[\'"]/i', $line, $matches)) {
                $requiredFiles[] = $matches[1];
            }
        }

        // Output the required files with their file paths
        if (empty($requiredFiles)) {
            echo "No required files found for $selectedFile.";
        } else {
            echo "<h3>Required Files for $selectedFile:</h3>";
            echo "<ul>";
            foreach ($requiredFiles as $file) {
                echo "<li>$file</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "Error: File not found or does not exist.";
    }

    $filePath = '.' . $selectedFile;
    echo "Full File Path: " . $filePath . "<br>";
    echo "Absolute File Path: " . realpath($filePath) . "<br>";

    $selectedFile = isset($_POST['file']) ? $_POST['file'] : '';
    $filePath = realpath('./' . $selectedFile); // Use realpath to get the absolute path
    
    if ($filePath !== false && file_exists($filePath)) {
        // File exists, process it
    } else {
        echo "Error: File not found or does not exist.";
    }
}
?>