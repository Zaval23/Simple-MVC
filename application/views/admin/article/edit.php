<?php 
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-articles-nav.php'); ?>

<h2><?= $editArticleTitle ?>
    <span>
        <?= $User->returnIfAllowed("admin/adminarticle/delete", 
            "<a href=" . WebRouter::link("admin/adminarticle/delete&id=" . $_GET['id']) 
            . ">[Удалить]</a>");?>
    </span>
</h2>

<form action="<?= WebRouter::link("admin/adminarticle/edit&id=" . $_GET['id']) ?>" method="post">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">

    <div class="form-group">
        <label for="title">Название статьи</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="Название статьи" required autofocus maxlength="255" value="<?= htmlspecialchars($article->title) ?>">
    </div>

    <div class="form-group">
        <label for="summary">Краткое описание</label>
        <textarea class="form-control" name="summary" id="summary" placeholder="Краткое описание статьи" required maxlength="1000" style="height: 5em;"><?= htmlspecialchars($article->summary) ?></textarea>
    </div>

    <div class="form-group">
        <label for="content">Содержание статьи</label>
        <textarea class="form-control" name="content" id="content" placeholder="HTML-содержание статьи" required maxlength="100000" style="height: 30em;"><?= htmlspecialchars($article->content) ?></textarea>
    </div>

    <div class="form-group">
        <label for="categoryId">Категория</label>
        <select class="form-control" name="categoryId" id="categoryId" onchange="updateSubcategories()" required>
            <option value="">(не выбрано)</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category->id ?>" <?= ($category->id == $article->categoryId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="subcategoryId">Подкатегория</label>
        <select class="form-control" name="subcategoryId" id="subcategoryId">
            <option value="">(не выбрано)</option>
            <?php 
            $subcategoriesByCategory = [];
            foreach ($subcategories as $subcat) {
                if (!isset($subcategoriesByCategory[$subcat->categoryId])) {
                    $subcategoriesByCategory[$subcat->categoryId] = [];
                }
                $subcategoriesByCategory[$subcat->categoryId][] = $subcat;
            }
            foreach ($subcategoriesByCategory as $catId => $subcats): 
                foreach ($subcats as $subcat): 
            ?>
                <option value="<?= $subcat->id ?>" 
                        data-category-id="<?= $subcat->categoryId ?>"
                        <?= ($subcat->id == $article->subcategoryId) ? 'selected' : '' ?>
                        class="subcategory-option category-<?= $catId ?>">
                    <?= htmlspecialchars($categories[$catId]->name ?? 'Unknown') ?> - <?= htmlspecialchars($subcat->name) ?>
                </option>
            <?php 
                endforeach;
            endforeach; 
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="publicationDate">Дата публикации</label>
        <input type="date" class="form-control" name="publicationDate" id="publicationDate" placeholder="YYYY-MM-DD" value="<?= $article->publicationDate ? date('Y-m-d', strtotime($article->publicationDate)) : date('Y-m-d') ?>">
    </div>

    <div class="form-group">
        <label for="authors">Авторы статьи</label>
        <select class="form-control" name="authors[]" id="authors" multiple size="5" style="min-height: 100px;">
            <?php foreach ($users as $user): ?>
                <option value="<?= $user->id ?>" <?= in_array($user->id, $authorIds) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user->login) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Используйте Ctrl (или Cmd на Mac) для выбора нескольких авторов</small>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" <?= $article->is_visible ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_visible">
                Статья видна на сайте
            </label>
        </div>
    </div>

    <div class="buttons">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения">
        <input type="submit" class="btn" name="cancel" value="Отмена">
    </div>
</form>

<script>
function updateSubcategories() {
    var categorySelect = document.getElementById('categoryId');
    var subcategorySelect = document.getElementById('subcategoryId');
    var selectedCategoryId = categorySelect.value;
    
    var options = subcategorySelect.getElementsByTagName('option');
    var selectedValue = subcategorySelect.value;
    var foundSelected = false;
    
    for (var i = 0; i < options.length; i++) {
        var option = options[i];
        var optionCategoryId = option.getAttribute('data-category-id');
        
        if (option.value === '') {
            continue;
        }
        
        if (optionCategoryId == selectedCategoryId && selectedCategoryId && selectedCategoryId != '') {
            option.style.display = '';
            if (option.value == selectedValue) {
                foundSelected = true;
            }
        } else {
            option.style.display = 'none';
        }
    }
    
    if (!foundSelected && selectedValue) {
        subcategorySelect.value = '';
    }
    
    if (!selectedCategoryId || selectedCategoryId == '') {
        subcategorySelect.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateSubcategories();
});
</script>

