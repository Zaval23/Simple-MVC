<?php use ItForFree\SimpleAsset\SimpleAssetManager; 

SimpleAssetManager::printJS();
?>

<div id="footer">
    Простая PHP CMS &copy; 2017. Все права принадлежат всем. ;) <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/login") ?>">Site Admin</a>
</div>

