<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-articles-nav.php'); ?>

<h2><?= $viewArticleTitle ?></h2>

<h3><?= htmlspecialchars($article->title) ?></h3>
<p><strong>Краткое описание:</strong> <?= htmlspecialchars($article->summary) ?></p>
<div><strong>Содержание:</strong><br><?= $article->content ?></div>
<p><strong>Дата публикации:</strong> <?= date('j F Y', strtotime($article->publicationDate)) ?></p>
<p><strong>Видимость:</strong> <?= $article->is_visible ? 'Видна' : 'Скрыта' ?></p>

<?php if (!empty($article->authors)): ?>
    <p><strong>Авторы:</strong> 
        <?php 
        $authorNames = [];
        foreach ($article->authors as $author) {
            $authorNames[] = htmlspecialchars($author->login);
        }
        echo implode(', ', $authorNames);
        ?>
    </p>
<?php endif; ?>

<p>
    <?= $User->returnIfAllowed("admin/adminarticle/edit", 
        "<a href=" . WebRouter::link("admin/adminarticle/edit&id={$article->id}") . " class='btn btn-primary'>Редактировать</a>");?>
    <a href="<?= WebRouter::link("admin/adminarticle/index") ?>" class="btn">Назад к списку</a>
</p>

