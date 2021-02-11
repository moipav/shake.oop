<?php

require_once __DIR__ . '/../header.php';


foreach ($articles as $article): ?>
    <h2><a href="../articles/<?=$article->getId()?>"><?=$article->getName()?></a></h2>

    <p><?= $article->getText() ?></p>

    <hr>
<?php endforeach;


require_once __DIR__ . '/../footer.php';
