<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-subcategories-nav.php'); ?>

<h2><?= $listSubcategoriesTitle ?></h2>

<?php if (!empty($subcategories)): ?>
<table class="table">
    <thead>
        <tr>
            <th>Подкатегория</th>
            <th>Категория</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subcategories as $subcategory): ?>
            <tr onclick="location='<?= WebRouter::link("admin/adminsubcategory/edit&id={$subcategory->id}") ?>'">
                <td><?= htmlspecialchars($subcategory->name) ?></td>
                <td>
                    <?= isset($categories[$subcategory->categoryId]) ? htmlspecialchars($categories[$subcategory->categoryId]->name) : 'Неизвестная категория' ?>
                </td>
                <td onclick="event.stopPropagation()">
                    <?= $User->returnIfAllowed("admin/adminsubcategory/edit",
                        "<a href=" . WebRouter::link("admin/adminsubcategory/edit&id={$subcategory->id}") 
                        . ">[Редактировать]</a>");?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><?= $totalRows ?> подкатегори<?= ($totalRows % 10 >= 2 && $totalRows % 10 <= 4 && ($totalRows % 100 < 10 || $totalRows % 100 >= 20)) ? 'и' : (($totalRows % 10 == 1 && $totalRows % 100 != 11) ? 'я' : 'й') ?> всего.</p>
<?php else: ?>
    <p>Список подкатегорий пуст.</p>
<?php endif; ?>

<p><a href="<?= WebRouter::link("admin/adminsubcategory/add") ?>" class="btn btn-primary">Добавить новую подкатегорию</a></p>

