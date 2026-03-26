<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LANShare | Secure Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar for Controls */
        .sidebar {
            width: 350px;
            background: #1e293b;
            padding: 25px;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
        }

        /* Main Content for Vault */
        .vault-area {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            text-align: center;
        }

        #qrcode {
            background: white;
            padding: 10px;
            border-radius: 8px;
            margin: 15px auto;
            display: inline-block;
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #334155;
            background: #0f172a;
            color: white;
            box-sizing: border-box;
        }

        button {
            background: #3b82f6;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }

        button:hover { background: #2563eb; }

        .btn-download-all { background: #10b981; margin-top: 10px; }
        .btn-download-all:hover { background: #059669; }
        
        .btn-delete { background: #ef4444 !important; margin-top: 8px; }
        .btn-delete:hover { background: #dc2626 !important; }

        /* Search Bar Styling */
        #vaultSearch {
            max-width: 500px;
            border: 1px solid #3b82f6;
            background: #1e293b;
            margin-bottom: 20px;
        }

        /* Vault Grid */
        .vault-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .file-card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            width: 200px;
            text-align: center;
            border: 1px solid #334155;
            transition: all 0.3s ease;
            position: relative;
        }

        .file-card:hover {
            box-shadow: 0 0 15px #3b82f6;
            transform: translateY(-5px);
        }

        .file-icon { font-size: 2.5rem; display: block; margin-bottom: 10px; }
        .file-name { font-size: 0.85rem; word-break: break-all; color: #94a3b8; display: block; margin-bottom: 10px;}

        .checkbox-container {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div style="text-align:center;">
            <h2>LANShare</h2>
            <div id="qrcode"></div>
            <p style="font-size:0.8rem; color:#94a3b8;">Scan to join</p>
            <hr style="border:0.5px solid #334155; margin: 15px 0;">
            
            <label style="display:block; text-align:left; font-size: 0.8rem;">Upload Files:</label>
            <input type="file" id="fileInput" multiple>
            <input type="password" id="password" placeholder="Encryption Key">
            <button onclick="encryptFile()">Securely Upload</button>
            
            <hr style="border:0.5px solid #334155; margin: 15px 0;">
            
            <button onclick="downloadAll()" class="btn-download-all">Download All Visible</button>
            <button onclick="deleteSelected()" class="btn-delete" style="display: none;" id="bulkDeleteBtn">Delete Selected Files</button>

            <hr style="border:0.5px solid #334155; margin: 15px 0;">
            <button onclick="document.querySelector('.vault-area').scrollIntoView({behavior: 'smooth'})" 
            style="background: #1e293b; border: 1px solid #3b82f6; margin-bottom: 5px;">
            📂 View Secure Vault</button>

            <button id="adminToggleBtn" onclick="enableAdmin()" 
            style="background: #0f172a; border: 1px solid #334155; font-size: 0.7rem; color: #94a3b8;">
            Admin Login</button>
        </div>
    </div>

    <div class="vault-area">
        <h2 style="margin-bottom: 25px;">Secure Vault</h2>
        
        <div style="max-width: 500px; margin: 0 auto;">
            <input type="text" id="vaultSearch" placeholder="🔍 Search file name..." onkeyup="filterFiles()">
            <input type="password" id="decryptPass" placeholder="Enter Key to Unlock Files">
        </div>

        <div id="fileGrid" class="vault-container"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="js/encrypt.js"></script>
    <script src="js/decrypt.js"></script>
ip
    <script>
        const myIP = "192.168.1.35";
        new QRCode(document.getElementById("qrcode"), `http://${myIP}/LANShare/index.php`);

        // 1. Ownership Logic
        function markAsMine(fileName) {
            let myFiles = JSON.parse(localStorage.getItem('myUploads') || '[]');
            if (!myFiles.includes(fileName)) {
                myFiles.push(fileName);
                localStorage.setItem('myUploads', JSON.stringify(myFiles));
            }
        }

        // 2. Icon Helper
        function getFileIcon(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            if (['jpg', 'png', 'gif', 'jpeg', 'webp'].includes(ext)) return '🖼️';
            if (['mp4', 'mkv', 'mov', 'webm'].includes(ext)) return '🎬';
            if (['pdf', 'txt', 'docx', 'csv'].includes(ext)) return '📄';
            return '📁';
        }

        // 3. Search Filter Logic
        function filterFiles() {
            const query = document.getElementById('vaultSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.file-card');
            cards.forEach(card => {
                const name = card.querySelector('.file-name').innerText.toLowerCase();
                card.style.display = name.includes(query) ? "block" : "none";
            });
        }

        // 4. Download All Visible Files
        async function downloadAll() {
            const password = document.getElementById('decryptPass').value;
            if (!password) {
                alert("Please enter the decryption key first!");
                return;
            }
            const visibleCards = Array.from(document.querySelectorAll('.file-card')).filter(c => c.style.display !== 'none');
            if (visibleCards.length === 0) return;

            if (confirm(`Decrypt and download ${visibleCards.length} files?`)) {
                for (const card of visibleCards) {
                    const name = card.querySelector('.file-name').innerText;
                    await downloadAndDecrypt(name);
                    await new Promise(r => setTimeout(r, 600)); // Prevent browser blocking multiple downloads
                }
            }
        }

        // 5. Bulk Delete Logic
        async function deleteSelected() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            const selected = Array.from(checkboxes).map(cb => cb.value);

            if (selected.length === 0) {
                alert("Please select at least one file to delete.");
                return;
            }

            if (confirm(`Are you sure you want to delete ${selected.length} files?`)) {
                for (const fileName of selected) {
                    await fetch(`delete.php?file=${fileName}`);
                    
                    // Update local ownership list
                    let myFiles = JSON.parse(localStorage.getItem('myUploads') || '[]');
                    myFiles = myFiles.filter(name => name !== fileName);
                    localStorage.setItem('myUploads', JSON.stringify(myFiles));
                }
                refreshVault();
            }
        }

        // 6. Integrated Refresh Logic
        function refreshVault() {
            const myFiles = JSON.parse(localStorage.getItem('myUploads') || '[]');
            fetch('get_files.php').then(res => res.json()).then(files => {
                const grid = document.getElementById('fileGrid');
                grid.innerHTML = '';
                
                let hasOwnFiles = false;

                files.forEach(file => {
                    const isMine = myFiles.includes(file);
                    if(isMine) hasOwnFiles = true;

                    // Only show checkbox for owner
                    const checkboxHtml = isMine 
                        ? `<div class="checkbox-container"><input type="checkbox" class="file-checkbox" value="${file}" style="width:18px; height:18px; cursor:pointer;"></div>` 
                        : `<p style="font-size:0.7rem; color:#64748b; margin-top:5px;">Read Only</p>`;

                    grid.innerHTML += `
                        <div class="file-card">
                            ${isMine ? checkboxHtml : ''}
                            <span class="file-icon">${getFileIcon(file)}</span>
                            <span class="file-name">${file}</span>
                            <button onclick="downloadAndDecrypt('${file}')">Unlock</button>
                            ${!isMine ? checkboxHtml : ''}
                        </div>`;
                });

                // Control bulk delete button visibility
                const bulkBtn = document.getElementById('bulkDeleteBtn');
                bulkBtn.style.display = hasOwnFiles ? 'block' : 'none';
                
                filterFiles(); // Maintain search results after refresh
            });
        }

        // Heartbeat
        setInterval(refreshVault, 5000);
        refreshVault();

    // 1. Add these variables at the top of your script
let isAdmin = false; 

function enableAdmin() {
    const pass = prompt("Enter Master Password:");
    if (pass === "1204") { // Set your own password here
        isAdmin = true;
        alert("Admin Mode Enabled. You can now delete anything.");
        refreshVault(); // Refresh to show buttons immediately
    } else {
        alert("Wrong password.");
    }
}

// 2. Update the refreshVault function to use the isAdmin flag


function refreshVault() {
    const myFiles = JSON.parse(localStorage.getItem('myUploads') || '[]');
    fetch('get_files.php').then(res => res.json()).then(files => {
        const grid = document.getElementById('fileGrid');
        grid.innerHTML = '';
        
        let hasDeleteAuthority = false;

        files.forEach(file => {
            // THE FIX: It now checks if you are the Admin OR the Owner
            const canDelete = myFiles.includes(file) || isAdmin;
            
            if(canDelete) hasDeleteAuthority = true;

            // Only generate checkbox if canDelete is true
            const checkboxHtml = canDelete 
                ? `<div class="checkbox-container"><input type="checkbox" class="file-checkbox" value="${file}" style="width:18px; height:18px; cursor:pointer;"></div>` 
                : `<p style="font-size:0.6rem; color:#64748b; margin-top:5px;">Read Only</p>`;

            grid.innerHTML += `
                <div class="file-card">
                    ${checkboxHtml}
                    <span class="file-icon">${getFileIcon(file)}</span>
                    <span class="file-name">${file}</span>
                    <button onclick="downloadAndDecrypt('${file}')">Unlock</button>
                </div>`;
        });

        // Update the Bulk Delete button visibility
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        if (bulkBtn) {
            bulkBtn.style.display = hasDeleteAuthority ? 'block' : 'none';
        }
        
        filterFiles();
    });
}
// Update the refreshVault logic to check for isAdmin
// Inside refreshVault(), change isMine to:
const isMine = myFiles.includes(file) || isAdmin;
    </script>
</body>
</html>