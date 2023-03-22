<?php
// Set a time limit for the script execution (in seconds)
set_time_limit(60); // 1 minute

// Check the Content-Length header to limit the input size
$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
$maxContentLength = 30 * 1024 * 1024; // 30 MB

if ($contentLength > $maxContentLength) {
    http_response_code(413); // Payload Too Large
    echo json_encode(array('error' => 'Input size exceeds the allowed limit (30 MB)'));
    exit;
}

if (isset($_FILES["image"])) {
    $target_dir = "images/tmp/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
}
?>
