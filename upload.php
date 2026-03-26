<?php
$target_dir = "uploads/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Check if our specific POST variables exist
if (isset($_POST['encrypted_contents']) && isset($_POST['filename'])) {
    
    $encryptedData = $_POST['encrypted_contents'];
    $originalName = $_POST['filename'];
    
    // We append '.enc' to show it is an encrypted file
    $savePath = $target_dir . $originalName . ".enc";

    // Save the string directly into a file
    if (file_put_contents($savePath, $encryptedData)) {
        echo "Success! Encrypted file saved as: " . $originalName . ".enc";
    } else {
        echo "Error: Could not write to disk.";
    }
} else {
    echo "Error: No data received.";
}
?>