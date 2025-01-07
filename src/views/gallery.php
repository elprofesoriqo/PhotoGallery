<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="static/styles/main.css">
</head>
<body>
<?php
include 'components/forgallery/header.php';
?>
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
                <input class="button" type="submit" name="add_to_selected" value="Save Selected">
            </h3>
            <?php foreach ($gallery as $picture): $counter++; $isChecked = 0; ?>
                <div class="gallery-item">
                    <p>Title: <?= htmlspecialchars($picture['name']) ?></p>
                    <a href="view?id=<?= htmlspecialchars($picture['_id']) ?>">
                        <img src="<?= "../../images/" . htmlspecialchars($picture['_id']) . "thu" . htmlspecialchars($picture['extension']) ?>"
                             alt="Thumbnail of <?= htmlspecialchars($picture['name']) ?>"
                             class="gallery-image">
                    </a>
                    <p>Author: <?= htmlspecialchars($picture['author']) ?></p>
                    <input type="hidden" name="id<?= $counter ?>" value="<?= htmlspecialchars($picture['_id']) ?>">
                        <?php if (!empty($selected)): ?>
                            <?php foreach ($selected as $id => $pic): ?>
                                <?php if($id == $picture['_id'] && $pic['amount']){
                                    $isChecked=1;
                                } ?>
                            <?php endforeach ?>
                        <?php endif ?>
                    <label>

                    Select:
                        <input type="checkbox" name="<?=$counter?>" value="<?= $picture['_id']?>"
                            <?php if ($isChecked): ?>
                                checked
                            <?php endif ?>
                        />
                    </label>
                </div>
            <?php endforeach ?>
        </form>
    <?php endif ?>
</div>
<?php
require_once 'components/forgallery/foot.php';
?>
</body>
</html>
