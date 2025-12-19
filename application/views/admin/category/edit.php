<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-categories-nav.php'); ?>

<h2><?= $editCategoryTitle ?>
    <span>
        <?= $User->returnIfAllowed("admin/admincategory/delete", 
            "<a href=" . WebRouter::link("admin/admincategory/delete&id=" . $_GET['id']) 
            . ">[Удалить]</a>");?>
    </span>
</h2>

<form action="<?= WebRouter::link("admin/admincategory/edit&id=" . $_GET['id']) ?>" method="post">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">

    <div class="form-group">
        <label for="name">Название категории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название категории" required autofocus maxlength="255" value="<?= htmlspecialchars($category->name) ?>">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea class="form-control" name="description" id="description" placeholder="Краткое описание категории" required maxlength="1000" style="height: 5em;"><?= htmlspecialchars($category->description) ?></textarea>
    </div>

    <div class="buttons">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения">
        <input type="submit" class="btn" name="cancel" value="Отмена">
    </div>
</form>

