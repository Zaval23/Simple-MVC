<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<ul class="nav">
    <?php if ($User->isAllowed("admin/adminarticle/index")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/adminarticle/index") ?>">Список статей</a>
    </li>
    <?php endif; ?>
    
    <?php if ($User->isAllowed("admin/adminarticle/add")): ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= WebRouter::link("admin/adminarticle/add") ?>"> + Добавить статью</a>
    </li>
    <?php endif; ?>
</ul>

