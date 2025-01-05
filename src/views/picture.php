<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image</title>
    <link rel="stylesheet" href="../web/static/styles/main.css">
</head>
<body>
<a href="gallery" class="cancel">&laquo; Back</a><br/>

<h1><?= $gallery['name'] ?></h1>

<p>Image author: <?= $gallery['author'] ?> </p>

<a href="<?= "../../images/".$gallery['_id']."wm".$gallery['extension'] ?>" target="_blank">
    <img src="<?= "../../images/".$gallery['_id']."wm".$gallery['extension'] ?>" alt="watermarked" /> <br/>
</a>

<br/>
<a href="delete?id=<?= $gallery['_id'] ?>">Delete</a>

<hr/>

</body>
</html>
