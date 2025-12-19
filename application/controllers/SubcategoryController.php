<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;

/**
 * Контроллер для архива по подкатегориям
 */
class SubcategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    /**
     * Архив статей по подкатегории
     */
    public function archiveAction()
    {
        $subcategoryId = $_GET['subcategoryId'] ?? $_GET['id'] ?? null;
        
        if (!$subcategoryId) {
            $this->redirect(\ItForFree\SimpleMVC\Router\WebRouter::link('homepage/index'));
            return;
        }
        
        $subcategoryModel = new SubcategoryModel();
        $subcategory = $subcategoryModel->getById($subcategoryId);
        
        if (!$subcategory) {
            $this->redirect(\ItForFree\SimpleMVC\Router\WebRouter::link('homepage/index'));
            return;
        }
        
        // Получаем категорию для подкатегории
        $categoryModel = new CategoryModel();
        $category = $categoryModel->getById($subcategory->categoryId);
        
        // Получаем статьи по подкатегории
        $articleModel = new ArticleModel();
        $articlesData = $articleModel->getListFiltered(null, $subcategoryId, 100000);
        
        // Получаем все категории для навигации
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $cat) {
            $categories[$cat->id] = $cat;
        }
        
        $pageHeading = $subcategory->name;
        $pageTitle = $pageHeading . " | Widget News";
        
        $this->view->addVar('subcategory', $subcategory);
        $this->view->addVar('category', $category);
        $this->view->addVar('articles', $articlesData['results']);
        $this->view->addVar('totalRows', $articlesData['totalRows']);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('pageHeading', $pageHeading);
        $this->view->addVar('pageTitle', $pageTitle);
        
        $this->view->render('subcategory/archive.php');
    }
}

