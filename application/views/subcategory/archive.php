<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h1><?= htmlspecialchars($pageHeading) ?></h1>

<?php if ($subcategory && $category): ?>
    <p class="categoryDescription">
        Подкатегория категории 
        <a href="<?= WebRouter::link("category/archive&categoryId={$category->id}") ?>">
            <?= htmlspecialchars($category->name) ?>
        </a>
    </p>
<?php endif; ?>

<ul id="headlines" class="archive">
<?php foreach ($articles as $article): ?>
    <li>
        <h2>
            <span class="pubDate">
                <?= date('j F Y', strtotime($article->publicationDate)) ?>
            </span>
            <a href="<?= WebRouter::link("article/view&articleId={$article->id}") ?>">
                <?= htmlspecialchars($article->title) ?>
            </a>

            <?php if ($article->categoryId && isset($categories[$article->categoryId])): ?>
                <span class="category">
                    in 
                    <a href="<?= WebRouter::link("category/archive&categoryId={$article->categoryId}") ?>">
                        <?= htmlspecialchars($categories[$article->categoryId]->name) ?>
                    </a>
                </span>
            <?php endif; ?>
            
            <?php if (!empty($article->authors)): ?>
                <span class="category">
                    , автор<?= count($article->authors) > 1 ? 'ы' : '' ?>: 
                    <?php 
                    $authorNames = [];
                    foreach ($article->authors as $author) {
                        $authorNames[] = htmlspecialchars($author->login);
                    }
                    echo implode(', ', $authorNames);
                    ?>
                </span>
            <?php endif; ?>
        </h2>
        <p class="summary"><?= htmlspecialchars($article->summary) ?></p>
    </li>
<?php endforeach; ?>
</ul>

<p><?= $totalRows ?> стат<?= ($totalRows % 10 >= 2 && $totalRows % 10 <= 4 && ($totalRows % 100 < 10 || $totalRows % 100 >= 20)) ? 'ьи' : (($totalRows % 10 == 1 && $totalRows % 100 != 11) ? 'ья' : 'ей') ?> всего.</p>

<p><a href="<?= WebRouter::link("homepage/index") ?>">Вернуться на главную страницу</a></p>

