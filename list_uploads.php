<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$targetDir = __DIR__ . '/uploads';

$files = array_filter(scandir($targetDir), function ($file) {
    return !in_array($file, ['.', '..']);
});

echo json_encode(array_values($files));
?>
