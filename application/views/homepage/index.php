<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>
<h1><?= $homepageTitle ?></h1>

<ul id="headlines">
<?php foreach ($articles as $article): ?>
    <li class='<?= $article->id ?>'>
        <h2>
            <span class="pubDate">
                <?= date('j F', strtotime($article->publicationDate)) ?>
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
            <?php else: ?>
                <span class="category">
                    Без категории
                </span>
            <?php endif; ?>
            
            <?php if ($article->subcategoryId && isset($subcategories[$article->subcategoryId])): ?>
                <span class="category">
                    , подкатегория 
                    <a href="<?= WebRouter::link("subcategory/archive&subcategoryId={$article->subcategoryId}") ?>">
                        <?= htmlspecialchars($subcategories[$article->subcategoryId]->name) ?>
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
        <p class="summary"><?= htmlspecialchars(mb_substr($article->content, 0, 50) . "...") ?></p>
        <a href="<?= WebRouter::link("article/view&articleId={$article->id}") ?>" class="showContent">
            Показать полностью
        </a>
    </li>
<?php endforeach; ?>
</ul>

<p><a href="<?= WebRouter::link("category/archive") ?>">Архив статей</a></p>
