<?php
// file_paths.php
$directoryTreeJson = file_get_contents('http://localhost/listing/file_tree.php');

if ($directoryTreeJson === false) {
    $response = [
        'error' => 'Failed to fetch directory tree JSON.'
    ];
    echo json_encode($response);
    exit;
}

$directoryTreeData = json_decode($directoryTreeJson, true);
if ($directoryTreeData === null) {
    $response = [
        'error' => 'Failed to decode directory tree JSON.'
    ];
    echo json_encode($response);
    exit;
}

// Add the 'id' property to each file/folder node
function addIdProperty(&$node, $parentId = '') {
    $node['id'] = $parentId . '/' . $node['text'];
    if (isset($node['children'])) {
        foreach ($node['children'] as &$child) {
            addIdProperty($child, $node['id']);
        }
    }
}

foreach ($directoryTreeData as &$node) {
    addIdProperty($node);
}

header('Content-Type: application/json');
echo json_encode($data);
?>