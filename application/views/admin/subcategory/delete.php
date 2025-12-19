<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-subcategories-nav.php'); ?>

<h2><?= $deleteSubcategoryTitle ?></h2>

<p>Вы уверены, что хотите удалить подкатегорию "<?= htmlspecialchars($subcategory->name) ?>"?</p>

<form method="post" action="<?= WebRouter::link("admin/adminsubcategory/delete&id=" . $_GET['id']) ?>">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    <input type="submit" class="btn btn-danger" name="deleteSubcategory" value="Удалить">
    <a href="<?= WebRouter::link("admin/adminsubcategory/index") ?>" class="btn">Отмена</a>
</form>

