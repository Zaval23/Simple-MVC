<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-articles-nav.php'); ?>

<h2><?= $listArticlesTitle ?></h2>

<?php if (!empty($articles)): ?>
<table class="table">
    <thead>
        <tr>
            <th>Дата публикации</th>
            <th>Статья</th>
            <th>Категория</th>
            <th>Подкатегория</th>
            <th>Авторы</th>
            <th>Видимость</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
            <tr onclick="location='<?= WebRouter::link("admin/adminarticle/edit&id={$article->id}") ?>'">
                <td><?= date('j M Y', strtotime($article->publicationDate)) ?></td>
                <td><?= htmlspecialchars($article->title) ?></td>
                <td>
                    <?php if ($article->categoryId && isset($categories[$article->categoryId])): ?>
                        <?= htmlspecialchars($categories[$article->categoryId]->name) ?>
                    <?php else: ?>
                        <em>Без категории</em>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($article->subcategoryId && isset($subcategories[$article->subcategoryId])): ?>
                        <?= htmlspecialchars($subcategories[$article->subcategoryId]->name) ?>
                    <?php else: ?>
                        <em>-</em>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($article->authors)): ?>
                        <?php 
                        $authorNames = [];
                        foreach ($article->authors as $author) {
                            $authorNames[] = htmlspecialchars($author->login);
                        }
                        echo implode(', ', $authorNames);
                        ?>
                    <?php else: ?>
                        <em>Нет авторов</em>
                    <?php endif; ?>
                </td>
                <td onclick="event.stopPropagation()" style="text-align: center;">
                    <form method="post" action="<?= WebRouter::link("admin/adminarticle/updateVisibility") ?>" style="display: inline; margin: 0; padding: 0;">
                        <input type="hidden" name="articleId" value="<?= $article->id ?>">
                        <input type="checkbox" name="is_visible" value="1" 
                            <?= $article->is_visible ? 'checked' : '' ?>
                            onchange="this.form.submit()" 
                            style="width: auto; display: inline; margin: 0; transform: scale(1.2);">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><?= $totalRows ?> стат<?= ($totalRows % 10 >= 2 && $totalRows % 10 <= 4 && ($totalRows % 100 < 10 || $totalRows % 100 >= 20)) ? 'ьи' : (($totalRows % 10 == 1 && $totalRows % 100 != 11) ? 'ья' : 'ей') ?> всего.</p>
<?php else: ?>
    <p>Список статей пуст.</p>
<?php endif; ?>

<p><a href="<?= WebRouter::link("admin/adminarticle/add") ?>" class="btn btn-primary">Добавить новую статью</a></p>

