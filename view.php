<?php
$dir = "uploads/";
// Get all files except the hidden system ones
$files = array_diff(scandir($dir), array('.', '..'));
?>
<!DOCTYPE html>
<html>
<head >
    
    <title>LANShare | Secure Vault</title>
    <!-- <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .file-card { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        button { background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
    </style> -->
     <style>
        body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #0f172a;
        color: #f8fafc;
        padding: 40px;
        }

        h2 { text-align: center; color: #3b82f6; }
 
/* The container for all your files */
        .vault-container {
        display: flex;
        flex-wrap: wrap; /* This fixes the long horizontal row */
        gap: 20px;
        justify-content: center;
        margin-top: 30px;
        }

/* Individual file cards */
        .file-card {
        background: #1e293b;
        padding: 20px;
        border-radius: 12px;
        width: 280px; /* Fixed width for a grid look */
        text-align: center;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        border: 1px solid #334155;
        }

        .file-name {
        display: block;
        margin-bottom: 15px;
        font-weight: bold;
        word-wrap: break-word; /* Prevents long names from breaking the card */
        }

        input[type="password"] {
        display: block;
        margin: 0 auto 20px auto;
        width: 300px;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #334155;
        background: #1e293b;
        color: white;
        }

        button {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        }
 
        button:hover { background: #2563eb; }
        </style>
</head>
<body>
    <div class="container">
        <h2>Secure Vault</h2>
        <input type="password" id="decryptPass" placeholder="Enter Key to Unlock Files">
        
        <div class="vault-container">
            <?php foreach($files as $file): ?>
                <div class="file-card">
                    <span class="file-name"><?php echo $file; ?></span>
                    <button onclick="downloadAndDecrypt('<?php echo $file; ?>')">Unlock & Download</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="js/decrypt.js"></script>
</body>
</html>