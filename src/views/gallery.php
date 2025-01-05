<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>
<div class="gallery-container">
    <?php
    $counter = 0;
    if (!isset($_SESSION['selected'])) {
        $_SESSION['selected'] = "";
    }
    $selected = $_SESSION['selected'];
    ?>

    <?php if (count($gallery)): ?>
        <form action="selected/add" method="post">
            <h3>
                <input class="button" type="submit" name="add_to_selected" value="&#x2022; Save Selected">
            </h3>
            <?php foreach ($gallery as $picture): $counter++; $isChecked = 0; ?>
                <div class="gallery-item">
                    <p>Title: <?= htmlspecialchars($picture['name']) ?></p>
                    <a href="view?id=<?= htmlspecialchars($picture['_id']) ?>">
                        <img src="<?= "../../images/" . htmlspecialchars($picture['_id']) . "min" . htmlspecialchars($picture['extension']) ?>"
                             alt="Thumbnail of <?= htmlspecialchars($picture['name']) ?>"
                             class="gallery-image">
                    </a>
                    <p>Author: <?= htmlspecialchars($picture['author']) ?></p>
                    <input type="hidden" name="id<?= $counter ?>" value="<?= htmlspecialchars($picture['_id']) ?>">
                    <label>
                        Select:
                        <input type="checkbox" name="<?= $counter ?>" value="<?= htmlspecialchars($picture['_id']) ?>"
                            <?php if (!empty($selected)): ?>
                                <?php foreach ($selected as $id => $pic): ?>
                                    <?php if ($id == $picture['_id'] && $pic['amount']): ?>
                                        checked
                                        <?php $isChecked = 1; ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            <?php endif ?>
                        >
                    </label>
                </div>
                <?php if ($counter % 3 == 0): ?>
                    <hr class="clear">
                <?php endif ?>
            <?php endforeach ?>
        </form>
    <?php else: ?>
        <p class="no-images">No photos available</p>
    <?php endif ?>
</div>
<hr class="clear">
</body>
</html>
