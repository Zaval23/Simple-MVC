
<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');


//ppre($User->explainAccess("admin/adminusers/index"));

?>

<nav>
    <ul>
        <li><a href="/">Главная</a></li>
        <?php  if ($User->isAllowed("login/login")): ?>
        <li><a href="<?= WebRouter::link("login/login") ?>">[Вход]</a></li>
        <?php endif; ?>
        <?php  if ($User->isAllowed("admin/adminarticle/index")): ?>
        <li><a href="<?= WebRouter::link("admin/adminarticle/index") ?>">Статьи</a></li>
        <?php endif; ?>
        <?php  if ($User->isAllowed("admin/admincategory/index")): ?>
        <li><a href="<?= WebRouter::link("admin/admincategory/index") ?>">Категории</a></li>
        <?php endif; ?>
        <?php  if ($User->isAllowed("admin/adminsubcategory/index")): ?>
        <li><a href="<?= WebRouter::link("admin/adminsubcategory/index") ?>">Подкатегории</a></li>
        <?php endif; ?>
        <?php  if ($User->isAllowed("admin/adminusers/index")): ?>
        <li><a href="<?= WebRouter::link("admin/adminusers/index") ?>">Пользователи</a></li>
        <?php endif; ?>
        <?php  if ($User->isAllowed("login/logout")): ?>
        <li><a href="<?= WebRouter::link("login/logout") ?>">Выход (<?= $User->userName ?>)</a></li>
        <?php endif; ?>
    </ul>
</nav>

