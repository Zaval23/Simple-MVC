<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<?php include('includes/admin-categories-nav.php'); ?>

<h2><?= $newCategoryTitle ?></h2>

<form action="<?= WebRouter::link("admin/admincategory/add") ?>" method="post">
    <div class="form-group">
        <label for="name">Название категории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название категории" required autofocus maxlength="255" value="">
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea class="form-control" name="description" id="description" placeholder="Краткое описание категории" required maxlength="1000" style="height: 5em;"></textarea>
    </div>

    <div class="buttons">
        <input type="submit" class="btn btn-primary" name="saveNewCategory" value="Сохранить">
        <input type="submit" class="btn" name="cancel" value="Отмена">
    </div>
</form>

