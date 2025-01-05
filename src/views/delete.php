<!DOCTYPE html>
<html>
<head>
    <title>Delete Image</title>
    <link rel="stylesheet" href="../web/static/styles/main.css">
</head>
<body>

<form method="post">
    Do you want to delete the image: <?= $gallery['name'] ?>?<br/>
    Image Author: <?= $gallery['author'] ?>

    <input type="hidden" name="id" value="<?= $gallery['_id'] ?>">

    <div><br/>
        <a href="gallery" class="cancel">Cancel</a>
        <input class="pad1" type="submit" value="Confirm"/>
    </div>
</form>

</body>
</html>
