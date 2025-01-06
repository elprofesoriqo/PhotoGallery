<?php

function &get_selected()
{
    if (!isset($_SESSION['selected'])) {
        $_SESSION['selected'] = [];

    }

    return $_SESSION['selected'];
}