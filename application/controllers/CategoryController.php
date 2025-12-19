<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;

/**
 * Контроллер для архива по категориям
 */
class CategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    /**
     * Архив статей по категории
     */
    public function archiveAction()
    {
        $categoryId = $_GET['categoryId'] ?? $_GET['id'] ?? null;
        
        $categoryModel = new CategoryModel();
        $category = null;
        
        if ($categoryId) {
            $category = $categoryModel->getById($categoryId);
        }
        
        $articleModel = new ArticleModel();
        $articlesData = $articleModel->getListFiltered($categoryId, null, 100000);
        
        // Получаем все категории для навигации
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $cat) {
            $categories[$cat->id] = $cat;
        }
        
        // Получаем все подкатегории
        $subcategoryModel = new SubcategoryModel();
        $subcategoriesData = $subcategoryModel->getList();
        $subcategories = [];
        foreach ($subcategoriesData['results'] as $subcat) {
            $subcategories[$subcat->id] = $subcat;
        }
        
        $pageHeading = $category ? $category->name : "Архив статей";
        $pageTitle = $pageHeading . " | Widget News";
        
        $this->view->addVar('category', $category);
        $this->view->addVar('articles', $articlesData['results']);
        $this->view->addVar('totalRows', $articlesData['totalRows']);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('pageHeading', $pageHeading);
        $this->view->addVar('pageTitle', $pageTitle);
        
        $this->view->render('category/archive.php');
    }
}

