<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <link rel="stylesheet" href="../web/static/styles/main.css"/>
</head>
<body>

<form method="post" enctype="multipart/form-data">
    <label>
        <span>Image Name:</span>
        <input type="text" name="name" value="<?= $gallery['name'] ?>" required/>
    </label>
    <label>
        <span>Author:</span>
        <input type="text" name="author" value="<?= $gallery['author'] ?>" required/>
    </label>
    <label>
        <span>File:</span>
        <input type="file" name="file" required/>
    </label>
    <label>
        <span>Watermark:</span>
        <input type="text" name="watermark" required/>
    </label>
    <label>
        <span>Description:</span>
        <textarea name="description" placeholder="Description..."><?= $gallery['description'] ?></textarea>
    </label>

    <input type="hidden" name="id" value="<?= $gallery['_id'] ?>">

    <div>
        <a href="gallery" class="cancel">Cancel</a>
        <span class="pad3"><input class="button" type="submit" value="Save"/></span>
    </div>
</form>

</body>
</html>
