<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-subcategories-nav.php'); ?>

<h2><?= $editSubcategoryTitle ?>
    <span>
        <?= $User->returnIfAllowed("admin/adminsubcategory/delete", 
            "<a href=" . WebRouter::link("admin/adminsubcategory/delete&id=" . $_GET['id']) 
            . ">[Удалить]</a>");?>
    </span>
</h2>

<form action="<?= WebRouter::link("admin/adminsubcategory/edit&id=" . $_GET['id']) ?>" method="post">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">

    <div class="form-group">
        <label for="name">Название подкатегории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название подкатегории" required autofocus maxlength="255" value="<?= htmlspecialchars($subcategory->name) ?>">
    </div>

    <div class="form-group">
        <label for="categoryId">Категория</label>
        <select class="form-control" name="categoryId" id="categoryId" required>
            <option value="">(выберите категорию)</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category->id ?>" <?= ($category->id == $subcategory->categoryId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="buttons">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения">
        <input type="submit" class="btn" name="cancel" value="Отмена">
    </div>
</form>

