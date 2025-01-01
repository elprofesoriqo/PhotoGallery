<?php
// Constants for file handling and image processing
define('UPLOAD_PATH', '../../images/');
define('MAX_FILE_SIZE', 1048576); // 1MB in bytes
define('THUMBNAIL_WIDTH', 200);
define('THUMBNAIL_HEIGHT', 125);

// Database connection handling
function connectToDatabase() {
    try {
        $mongo = new MongoDB\Client(
            "mongodb://localhost:27017/wai",
            [
                'username' => 'wai_web',
                'password' => 'w@i_w3b',
                'connectTimeoutMS' => 2000,
                'retryWrites' => true
            ]
        );
        return $mongo->wai;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Unable to connect to database. Please try again later.");
    }
}

// Image processing functions
function createThumbnail($sourceImage) {
    $originalWidth = imagesx($sourceImage);
    $originalHeight = imagesy($sourceImage);

    $thumbnail = imagecreatetruecolor(THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);

    // Preserve transparency for PNG images
    imagealphablending($thumbnail, false);
    imagesavealpha($thumbnail, true);

    imagecopyresampled(
        $thumbnail, $sourceImage,
        0, 0, 0, 0,
        THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT,
        $originalWidth, $originalHeight
    );

    return $thumbnail;
}

function addWatermark($sourceImage, $watermarkText) {
    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);

    $watermarked = imagecreatetruecolor($width, $height);
    imagecopy($watermarked, $sourceImage, 0, 0, 0, 0, $width, $height);

    $white = imagecolorallocate($watermarked, 255, 255, 255);
    $black = imagecolorallocate($watermarked, 0, 0, 0);

    $angle = -25;
    $fontSize = 5;
    $spacing = 150;

    for ($y = -$height; $y < $height * 2; $y += $spacing) {
        for ($x = -$width; $x < $width * 2; $x += strlen($watermarkText) * 15) {
            imagestring($watermarked, $fontSize, $x + 1, $y + 1, $watermarkText, $black);
            imagestring($watermarked, $fontSize, $x, $y, $watermarkText, $white);
        }
    }

    return $watermarked;
}

function processImage($sourceFile, $imageType, $id, $watermark) {
    // Create image resource based on type
    $sourceImage = $imageType === 'image/jpeg' ?
        imagecreatefromjpeg($sourceFile) :
        imagecreatefrompng($sourceFile);

    if (!$sourceImage) {
        throw new Exception('Failed to process image. The file may be corrupted.');
    }

    $fileExtension = getFileExtension($imageType);

    // Process original version
    $originalPath = UPLOAD_PATH . $id . $fileExtension;
    if (!imagejpeg($sourceImage, $originalPath, 90)) {
        throw new Exception('Failed to save original image');
    }

    // Process thumbnail version
    $thumbnail = createThumbnail($sourceImage);
    $thumbnailPath = UPLOAD_PATH . $id . '_thumb' . $fileExtension;
    if (!imagejpeg($thumbnail, $thumbnailPath, 90)) {
        throw new Exception('Failed to save thumbnail');
    }
    imagedestroy($thumbnail);

    // Process watermarked version
    $watermarked = addWatermark($sourceImage, $watermark);
    $watermarkedPath = UPLOAD_PATH . $id . '_wm' . $fileExtension;
    if (!imagejpeg($watermarked, $watermarkedPath, 90)) {
        throw new Exception('Failed to save watermarked image');
    }
    imagedestroy($watermarked);
    imagedestroy($sourceImage);
}

// File upload handling
function handleImageUpload($file, $watermark) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception(getUploadErrorMessage($file['error']));
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds the maximum limit of 1MB');
    }

    if (!in_array($file['type'], ['image/jpeg', 'image/png'])) {
        throw new Exception('Only JPEG and PNG files are allowed');
    }

    $id = new MongoDB\BSON\ObjectId();
    $tempPath = $file['tmp_name'];
    $targetPath = UPLOAD_PATH . $id . getFileExtension($file['type']);

    if (!move_uploaded_file($tempPath, $targetPath)) {
        throw new Exception('Failed to upload file');
    }

    processImage($targetPath, $file['type'], $id, $watermark);
    return $id;
}

// User authentication and management
function authenticateUser($db, $login, $password) {
    $user = $db->users->findOne(['login' => $login]);

    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid login credentials');
    }

    return [
        'id' => $user['_id'],
        'login' => $user['login'],
        'email' => $user['email']
    ];
}

function createUser($db, $userData) {
    if ($db->users->findOne(['login' => $userData['login']])) {
        throw new Exception('Username already exists');
    }

    if ($db->users->findOne(['email' => $userData['email']])) {
        throw new Exception('Email already registered');
    }

    $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT);
    $userData['created_at'] = new MongoDB\BSON\UTCDateTime();

    $result = $db->users->insertOne($userData);
    return $result->getInsertedId();
}

// Image and gallery management
function getImages($db, $page = 1, $limit = 20, $filter = []) {
    $skip = ($page - 1) * $limit;
    $options = [
        'limit' => $limit,
        'skip' => $skip,
        'sort' => ['created_at' => -1]
    ];

    $images = $db->images->find($filter, $options)->toArray();
    $total = $db->images->countDocuments($filter);

    return [
        'images' => $images,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ]
    ];
}

function saveImage($db, $imageData) {
    $imageData['created_at'] = new MongoDB\BSON\UTCDateTime();
    $result = $db->images->insertOne($imageData);
    return $result->getInsertedId();
}

// Helper functions
function getFileExtension($mimeType) {
    $extensions = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png'
    ];
    return $extensions[$mimeType] ?? '.jpg';
}

function getUploadErrorMessage($errorCode) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds PHP maximum file size limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum file size limit',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    return $errorMessages[$errorCode] ?? 'Unknown upload error occurred';
}