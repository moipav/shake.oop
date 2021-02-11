<?php
require_once __DIR__ . '/../header.php';

?>
    <h2><?= $article->getName() ?></h2>
    <p><?= $article->getText() ?></p>
    <p>Автор:  <?= $article->getAuthor()->getNickname() ?></p>

    <?php if(!empty($user) && $user->getRole() === 'admin'):?>
    <a href="/articles/<?= $article->getId() ?>/edit">редактировать</a>
    <?php endif;?>
    <hr>


<?php


require_once __DIR__ . '/../footer.php';