<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<?php include('includes/admin-subcategories-nav.php'); ?>

<h2><?= $newSubcategoryTitle ?></h2>

<form action="<?= WebRouter::link("admin/adminsubcategory/add") ?>" method="post">
    <div class="form-group">
        <label for="name">Название подкатегории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название подкатегории" required autofocus maxlength="255" value="">
    </div>

    <div class="form-group">
        <label for="categoryId">Категория</label>
        <select class="form-control" name="categoryId" id="categoryId" required>
            <option value="">(выберите категорию)</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category->id ?>"><?= htmlspecialchars($category->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="buttons">
        <input type="submit" class="btn btn-primary" name="saveNewSubcategory" value="Сохранить">
        <input type="submit" class="btn" name="cancel" value="Отмена">
    </div>
</form>

