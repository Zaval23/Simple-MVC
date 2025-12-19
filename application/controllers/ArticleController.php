<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;

/**
 * Контроллер для просмотра статей
 */
class ArticleController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    /**
     * Просмотр конкретной статьи
     */
    public function viewAction()
    {
        $articleId = $_GET['articleId'] ?? $_GET['id'] ?? null;
        
        if (!$articleId) {
            // Редирект на главную если нет ID
            $this->redirect(\ItForFree\SimpleMVC\Router\WebRouter::link('homepage/index'));
            return;
        }
        
        $articleModel = new ArticleModel();
        $article = $articleModel->getById($articleId);
        
        if (!$article) {
            // Статья не найдена - редирект
            $this->redirect(\ItForFree\SimpleMVC\Router\WebRouter::link('homepage/index'));
            return;
        }
        
        // Получаем категорию
        $category = null;
        if ($article->categoryId) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getById($article->categoryId);
        }
        
        // Получаем подкатегорию
        $subcategory = null;
        if ($article->subcategoryId) {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->getById($article->subcategoryId);
        }
        
        // Получаем все категории для навигации
        $categoryModel = new CategoryModel();
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $cat) {
            $categories[$cat->id] = $cat;
        }
        
        $this->view->addVar('article', $article);
        $this->view->addVar('category', $category);
        $this->view->addVar('subcategory', $subcategory);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('pageTitle', htmlspecialchars($article->title));
        
        $this->view->render('article/view.php');
    }
}

