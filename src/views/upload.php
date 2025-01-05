<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>
<div class="upload-container">
    <h2 class="upload-title">Upload New Image</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
            <label for="file">Image File</label>
            <input type="file" id="file" name="file" required accept="image/jpeg,image/png">
            <small class="form-text">Max file size: 1MB. Allowed types: JPEG, PNG</small>
        </div>

        <div class="form-group">
            <label for="name">Image Title</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" required>
        </div>

        <div class="form-group">
            <label for="watermark">Watermark Text</label>
            <input type="text" id="watermark" name="watermark" required>
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="/gallery" class="btn btn-secondary">Back to Gallery</a>
        </div>
    </form>
</div>
</body>
</html>
