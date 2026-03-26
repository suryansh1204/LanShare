<?php
$dir = "uploads/";
$files = array_diff(scandir($dir), array('.', '..'));
// Send the list back as a simple JSON array
echo json_encode(array_values($files));
?>