<?php

$title = $title ?? 'Главная ';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../../www/styles/style.css">
</head>
<body>
<table class="layout">
    <tr>
        <td colspan="2" class="header">
            Мой блог

        </td>
    </tr>
    <tr>
        <td colspan="2" class="header">

            <?php
            \MyProject\Controllers\UsersController::isAuth($user);
            ?>

        </td>
    </tr>
    <tr>
        <td>