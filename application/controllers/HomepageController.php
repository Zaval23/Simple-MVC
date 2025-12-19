<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;

/**
 * Контроллер для домашней страницы
 */
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Название страницы
     */
    public $homepageTitle = "Домашняя страница";
    
    /**
     * @var string Путь к файлу макета 
     */
    public string $layoutPath = 'main.php';
    
    /**
     * Количество статей на главной странице
     */
    public int $HOMEPAGE_NUM_ARTICLES = 5;
      
    /**
     * Выводит на экран главную страницу со списком последних статей
     */
    public function indexAction()
    {
        $articleModel = new ArticleModel();
        $categoryModel = new CategoryModel();
        $subcategoryModel = new SubcategoryModel();
        
        // Получаем последние статьи
        $articlesData = $articleModel->getListFiltered(null, null, $this->HOMEPAGE_NUM_ARTICLES);
        
        // Получаем все категории для навигации
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $category) {
            $categories[$category->id] = $category;
        }
        
        // Получаем все подкатегории
        $subcategoriesData = $subcategoryModel->getList();
        $subcategories = [];
        foreach ($subcategoriesData['results'] as $subcategory) {
            $subcategories[$subcategory->id] = $subcategory;
        }
        
        $this->view->addVar('articles', $articlesData['results']);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('homepageTitle', $this->homepageTitle);
        
        $this->view->render('homepage/index.php');
    }
}

