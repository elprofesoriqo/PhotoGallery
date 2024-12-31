<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Image</title>
    <link rel="stylesheet" href="assets/styles/upload.scss">
</head>
<body>
<?php
// Upload handling logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maxFileSize = 1 * 1024 * 1024; // 1 MB in bytes
    $allowedTypes = ['image/jpeg', 'image/jpg'];
    $uploadDir = 'images/';

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $author = isset($_POST['author']) ? $_POST['author'] : '';

        // Validate file size
        if ($file['size'] > $maxFileSize) {
            $error = 'File size must be less than 1 MB';
        }
        // Validate file type
        elseif (!in_array($file['type'], $allowedTypes)) {
            $error = 'Only JPG/JPEG files are allowed';
        }
        else {
            // Create upload directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $filename = uniqid() . '_' . basename($file['name']);
            $destination = $uploadDir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $success = 'File uploaded successfully';
            } else {
                $error = 'Failed to upload file';
            }
        }
    }
}
?>

<div class="container">
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>

    <form class="upload-form" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="file">Image:</label>
            <input class="form-control-file" type="file" name="file" id="file" required>
        </div>
        <div class="form-group">
            <label for="title">Title:</label>
            <input class="form-control" type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input class="form-control" type="text" name="author" id="author" required>
        </div>
        <button class="btn-primary" type="submit">Upload</button>
    </form>
</div>
</body>
</html>