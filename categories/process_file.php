<?php
// Get the selected file from the AJAX request
$selectedFile = isset($_POST['file']) ? $_POST['file'] : '';
echo "Received file path: $selectedFile";

if (!empty($selectedFile)) {
    $jsonFile = 'directory_tree.json';
    if (file_exists($jsonFile)) {
        $directoryTreeJson = file_get_contents($jsonFile);
        if ($directoryTreeJson === false) {
            echo "Error: Failed to read directory tree JSON file.";
            exit;
        }
        $directoryTreeData = json_decode($directoryTreeJson, true);

        // Find the file in the directory tree and get its full path
        $fullFilePath = findFilePath($directoryTreeData, $selectedFile);
        if ($fullFilePath !== false && file_exists($fullFilePath)) {
            // If the file exists, read its content
            $fileContent = file_get_contents($fullFilePath);

            // Process required files and output them
            $requiredFiles = [];
            $lines = explode("\n", $fileContent);
            foreach ($lines as $line) {
                // Check for PHP includes
                if (preg_match('/\b(?:include|require(?:_once)?)\\s*\\(\\s*[\'"]([^\'"]+)[\'"]\\s*\\)/', $line, $matches)) {
                    $requiredFiles[] = $matches[1];
                }
                // Check for linked CSS files
                elseif (preg_match('/<link\\s+[^>]*href\\s*=\\s*[\'"]([^\'"]+)[\'"]\\s*\\/?>/i', $line, $matches)) {
                    $requiredFiles[] = $matches[1];
                }
                // Check for linked JavaScript files
                elseif (preg_match('/<script\\s+[^>]*src\\s*=\\s*[\'"]([^\'"]+)[\'"]\\s*><\\/script>/i', $line, $matches)) {
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
            // File does not exist, handle error
            echo "Error: File does not exist.";
        }
    } else {
        echo "Error: Directory tree JSON file not found.";
        exit;
    }
} else {
    // No file path provided, handle error
    echo "Error: No file path provided.";
}

// Function to find the full path of a file in the directory tree
function findFilePath($directoryTreeData, $fileName, $basePath = '') {
    foreach ($directoryTreeData as $key => $value) {
        if (is_array($value)) {
            $newBasePath = $basePath !== '' ? $basePath . '/' . $key : $key;
            $filePath = findFilePath($value, $fileName, $newBasePath);
            if ($filePath !== false) {
                return $filePath;
            }
        } elseif ($value === $fileName) {
            return $basePath !== '' ? $basePath . '/' . $value : $value;
        }
    }
    return false;
}
?>