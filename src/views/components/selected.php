<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected</title>
    <link rel="stylesheet" href="assets/styles/main.css"/>
</head>
<body>
<?php
include_once("addpicture.php");
?>

<h1>SELECTED</h1>

<div class="container">

    <?php
    $counter = 0;
    if (!isset($_SESSION['selected'])) {
        $_SESSION['selected'] = "";
    }
    $selected = $_SESSION['selected'];
    ?>

    <?php if (count($gallery)): ?>
        <?php foreach ($gallery as $picture): $isChecked = 0; ?>
            <?php if (!empty($selected)): $item = $picture['_id']; ?>
                <?php if ($selected["$item"]['amount']): $counter += 1; ?>
                    <div class="singleItem">
                        <p>Title: <?= $picture['name'] ?></p>
                        <a href="view?id=<?= $picture['_id'] ?>">
                            <img src="<?= "../../images/".$picture['_id']."min".$picture['extension'] ?>" alt="miniature"/>
                        </a>
                        <p>Author: <?= $picture['author'] ?></p>

                        <input type="hidden" name="id<?=$counter?>" value="<?= $picture['_id'] ?>"/>

                        <?php if (!empty($selected)): ?>
                            <?php foreach ($selected as $id => $pict): ?>
                                <?php if ($id == $picture['_id'] && $pict['amount']) {
                                    $isChecked = 1;
                                } ?>
                            <?php endforeach ?>
                        <?php endif ?>
                        Selection:
                        <input type="checkbox" disabled name="<?=$counter?>" value="<?= $picture['_id']?>"
                            <?php if ($isChecked): ?>
                                checked
                            <?php endif ?>
                        />
                    </div>

                    <?php if ($counter % 3 == 0) {
                        $clean='<hr class="clear"/>';
                        require($clean);
                    }?>
                <?php endif?>
            <?php endif?>
        <?php endforeach ?>
    <?php else: ?>
        No images
    <?php endif ?>

</div>
<hr class="clear"/>

</body>
</html>
