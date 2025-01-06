<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected</title>
    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>

<div class="upload-container">
    <h1 class="upload-title">SELECTED</h1>

    <div class="gallery-container">
        <?php
        $counter = 0;
        if (!isset($_SESSION['selected'])) {
            $_SESSION['selected'] = [];
        }
        $selected = $_SESSION['selected'];
        ?>

        <?php if (count($gallery)): ?>
            <?php foreach ($gallery as $picture): $isChecked = 0; ?>
                <?php if (!empty($selected)): $item = $picture['_id']; ?>
                    <?php
                    // Sprawdzamy, czy dany element istnieje w $_SESSION['selected']
                    if (isset($selected["$item"]) && $selected["$item"]['amount']) {
                        $counter += 1;
                        ?>
                        <div class="gallery-item">
                            <p>Title: <?= $picture['name'] ?></p>
                            <a href="view?id=<?= $picture['_id'] ?>">
                                <img src="<?= "../../images/".$picture['_id']."thu".$picture['extension'] ?>"
                                     alt="thu"
                                     class="gallery-image"/>
                            </a>
                            <p>Author: <?= $picture['author'] ?></p>

                            <input type="hidden" name="id<?=$counter?>" value="<?= $picture['_id'] ?>"/>

                            <form action="selected/remove_from_selected" method="POST">
                                <input type="hidden" name="picture_id" value="<?= $picture['_id'] ?>"/>
                                <button type="submit" class="remove-button">Remove</button>
                            </form>

                        </div>

                    <?php } ?>
                <?php endif ?>
            <?php endforeach ?>
        <?php else: ?>
            <p class="alert alert-danger">No images</p>
        <?php endif ?>
    </div>
</div>
</body>
</html>
