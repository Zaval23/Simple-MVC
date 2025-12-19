<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-categories-nav.php'); ?>

<h2><?= $deleteCategoryTitle ?></h2>

<p>Вы уверены, что хотите удалить категорию "<?= htmlspecialchars($category->name) ?>"?</p>

<form method="post" action="<?= WebRouter::link("admin/admincategory/delete&id=" . $_GET['id']) ?>">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    <input type="submit" class="btn btn-danger" name="deleteCategory" value="Удалить">
    <a href="<?= WebRouter::link("admin/admincategory/index") ?>" class="btn">Отмена</a>
</form>

