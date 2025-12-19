<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h1 style="width: 75%;"><?= htmlspecialchars($article->title) ?></h1>
<div style="width: 75%; font-style: italic;"><?= htmlspecialchars($article->summary) ?></div>
<div style="width: 75%;"><?= $article->content ?></div>

<p class="pubDate">
    Опубликовано <?= date('j F Y', strtotime($article->publicationDate)) ?>
    
    <?php if ($category): ?>
        в категории 
        <a href="<?= WebRouter::link("category/archive&categoryId={$category->id}") ?>">
            <?= htmlspecialchars($category->name) ?>
        </a>
    <?php endif; ?>
    
    <?php if ($subcategory): ?>
        , подкатегория 
        <a href="<?= WebRouter::link("subcategory/archive&subcategoryId={$subcategory->id}") ?>">
            <?= htmlspecialchars($subcategory->name) ?>
        </a>
    <?php endif; ?>
    
    <?php if (!empty($article->authors)): ?>
        <br>Авторы: 
        <?php 
        $authorNames = [];
        foreach ($article->authors as $author) {
            $authorNames[] = htmlspecialchars($author->login);
        }
        echo implode(', ', $authorNames);
        ?>
    <?php endif; ?>
</p>

<p><a href="<?= WebRouter::link("homepage/index") ?>">Вернуться на главную страницу</a></p>

