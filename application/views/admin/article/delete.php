<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-articles-nav.php'); ?>

<h2><?= $deleteArticleTitle ?></h2>

<p>Вы уверены, что хотите удалить статью "<?= htmlspecialchars($article->title) ?>"?</p>

<form method="post" action="<?= WebRouter::link("admin/adminarticle/delete&id=" . $_GET['id']) ?>">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    <input type="submit" class="btn btn-danger" name="deleteArticle" value="Удалить">
    <a href="<?= WebRouter::link("admin/adminarticle/index") ?>" class="btn">Отмена</a>
</form>

