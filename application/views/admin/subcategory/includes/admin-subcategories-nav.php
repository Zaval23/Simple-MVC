<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<ul class="nav">
    <?php if ($User->isAllowed("admin/adminsubcategory/index")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/adminsubcategory/index") ?>">Список подкатегорий</a>
    </li>
    <?php endif; ?>
    
    <?php if ($User->isAllowed("admin/adminsubcategory/add")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/adminsubcategory/add") ?>"> + Добавить подкатегорию</a>
    </li>
    <?php endif; ?>
</ul>

