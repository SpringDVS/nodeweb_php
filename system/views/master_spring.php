<?php
$size = strlen($response);
header('Content-Type: application/octet-stream');
header("Content-Length: $size");

echo $response;
