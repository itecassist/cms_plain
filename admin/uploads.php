<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = handle_upload($_FILES['file']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['filename'])){
        $file_path = UPLOADS_DIR . '/' . basename($data['filename']);
        if(file_exists($file_path)){
            unlink($file_path);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'File not found']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No filename provided']);
    }
    exit;
}

$uploads = glob(UPLOADS_DIR . '/*.*');
$page_title = 'Manage Images';
include 'includes/admin-header.php';
?>
    <style>
        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .upload-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .upload-section h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover {
            background: #f8f9ff;
        }
        .upload-area.dragging {
            background: #f0f0ff;
            border-color: #5568d3;
        }
        #file-input {
            display: none;
        }
        .gallery {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .gallery h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .image-card {
            position: relative;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .image-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        .image-info {
            padding: 10px;
            background: #f9f9f9;
        }
        .image-name {
            font-size: 12px;
            color: #666;
            word-break: break-all;
            margin-bottom: 8px;
        }
        .image-path {
            font-size: 11px;
            color: #999;
            font-family: monospace;
            background: #f0f0f0;
            padding: 5px;
            border-radius: 3px;
            margin-bottom: 8px;
            word-break: break-all;
        }
        .copy-btn {
            width: 100%;
            padding: 6px;
            font-size: 12px;
            margin-bottom: 2px;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="upload-section">
            <h2>Upload New Image</h2>
            <div class="upload-area" id="upload-area">
                <p style="font-size: 48px; margin-bottom: 10px;">📁</p>
                <p style="font-size: 16px; color: #333; margin-bottom: 5px;">Click to upload or drag and drop</p>
                <p style="font-size: 14px; color: #666;">JPG, PNG, GIF, WebP (Max 5MB)</p>
                <input type="file" id="file-input" accept="image/*" multiple>
            </div>
            <div id="upload-status"></div>
        </div>
        
        <div class="gallery">
            <h2>Uploaded Images (<?php echo count($uploads); ?>)</h2>
            <?php if (empty($uploads)): ?>
                <p style="text-align: center; color: #666; padding: 40px;">No images uploaded yet.</p>
            <?php else: ?>
                <div class="image-grid">
                    <?php foreach (array_reverse($uploads) as $upload): ?>
                        <?php $filename = basename($upload); ?>
                        <div class="image-card">
                            <img src="../assets/img/<?php echo htmlspecialchars($filename); ?>" alt="<?php echo htmlspecialchars($filename); ?>">
                            <div class="image-info">
                                <div class="image-name"><?php echo htmlspecialchars($filename); ?></div>
                                <div><?php list($width, $height, $type, $attr) = getimagesize("../assets/img/" . htmlspecialchars($filename)); echo $width . "x" . $height; ?></div>
                                <div class="image-path">assets/img/<?php echo htmlspecialchars($filename); ?></div>
                                <button class="btn btn-primary copy-btn" onclick="copyPath('assets/img/<?php echo htmlspecialchars($filename); ?>')">Copy Path</button>
                                <button class="btn btn-secondary copy-btn" onclick="window.open('../assets/img/<?php echo htmlspecialchars($filename); ?>', '_blank')">View Image</button>
                                <button class="btn btn-danger  copy-btn" onclick="delImg('<?php echo htmlspecialchars($filename); ?>')">Delete Image</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file-input');
        const uploadStatus = document.getElementById('upload-status');
        
        function delImg(img){
            if(confirm('Are you sure you want to delete this image?')){
                fetch('uploads.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ filename: img })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Image deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting image: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error deleting image.');
                });
            }
            
        }
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragging');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragging');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragging');
            handleFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        function handleFiles(files) {
            uploadStatus.innerHTML = '';
            
            Array.from(files).forEach(file => {
                const formData = new FormData();
                formData.append('file', file);
                
                fetch('uploads.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadStatus.innerHTML += `<div class="alert alert-success">✓ ${file.name} uploaded successfully!</div>`;
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        uploadStatus.innerHTML += `<div class="alert alert-error">✗ ${file.name}: ${data.error}</div>`;
                    }
                })
                .catch(error => {
                    uploadStatus.innerHTML += `<div class="alert alert-error">✗ ${file.name}: Upload failed</div>`;
                });
            });
        }
        
        function copyPath(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('Path copied to clipboard!\n\n' + path);
            });
        }
    </script>
</body>
</html>
