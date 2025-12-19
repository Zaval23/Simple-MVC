<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<ul class="nav">
    <?php if ($User->isAllowed("admin/admincategory/index")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/admincategory/index") ?>">Список категорий</a>
    </li>
    <?php endif; ?>
    
    <?php if ($User->isAllowed("admin/admincategory/add")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/admincategory/add") ?>"> + Добавить категорию</a>
    </li>
    <?php endif; ?>
</ul>

