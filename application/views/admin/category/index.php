<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-categories-nav.php'); ?>

<h2><?= $listCategoriesTitle ?></h2>

<?php if (!empty($categories)): ?>
<table class="table">
    <thead>
        <tr>
            <th>Категория</th>
            <th>Описание</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr onclick="location='<?= WebRouter::link("admin/admincategory/edit&id={$category->id}") ?>'">
                <td><?= htmlspecialchars($category->name) ?></td>
                <td><?= htmlspecialchars($category->description) ?></td>
                <td onclick="event.stopPropagation()">
                    <?= $User->returnIfAllowed("admin/admincategory/edit",
                        "<a href=" . WebRouter::link("admin/admincategory/edit&id={$category->id}") 
                        . ">[Редактировать]</a>");?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><?= $totalRows ?> категор<?= ($totalRows % 10 >= 2 && $totalRows % 10 <= 4 && ($totalRows % 100 < 10 || $totalRows % 100 >= 20)) ? 'ии' : (($totalRows % 10 == 1 && $totalRows % 100 != 11) ? 'ия' : 'ий') ?> всего.</p>
<?php else: ?>
    <p>Список категорий пуст.</p>
<?php endif; ?>

<p><a href="<?= WebRouter::link("admin/admincategory/add") ?>" class="btn btn-primary">Добавить новую категорию</a></p>

