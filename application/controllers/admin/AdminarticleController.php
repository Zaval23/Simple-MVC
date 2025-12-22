<?php

namespace application\controllers\admin;

use application\models\ArticleModel;
use application\models\CategoryModel;
use application\models\SubcategoryModel;
use application\models\UserModel;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для управления статьями в админке
 */
class AdminarticleController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],
        ['allow' => false, 'roles' => ['?', '@']],
    ];
    
    /**
     * Список всех статей для админки
     */
    public function indexAction()
    {
        $categoryId = $_GET['categoryId'] ?? null;
        $subcategoryId = $_GET['subcategoryId'] ?? null;
        
        $articleModel = new ArticleModel();
        $articlesData = $articleModel->getListForAdmin($categoryId, $subcategoryId);
        
        $categoryModel = new CategoryModel();
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $cat) {
            $categories[$cat->id] = $cat;
        }
        
        $subcategoryModel = new SubcategoryModel();
        $subcategoriesData = $subcategoryModel->getList();
        $subcategories = [];
        foreach ($subcategoriesData['results'] as $subcat) {
            $subcategories[$subcat->id] = $subcat;
        }
        
        $this->view->addVar('articles', $articlesData['results']);
        $this->view->addVar('totalRows', $articlesData['totalRows']);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('listArticlesTitle', "Список статей");
        
        $this->view->render('admin/article/index.php');
    }
    
    /**
     * Просмотр конкретной статьи
     */
    public function viewAction()
    {
        $id = $_GET['id'];
        
        $articleModel = new ArticleModel();
        $article = $articleModel->getById($id);
        
        if (!$article) {
            $this->redirect(WebRouter::link('admin/adminarticle/index'));
            return;
        }
        
        $this->view->addVar('article', $article);
        $this->view->addVar('viewArticleTitle', "Просмотр статьи");
        
        $this->view->render('admin/article/view.php');
    }
    
    /**
     * Добавление новой статьи
     */
    public function addAction()
    {
        if (!empty($_POST['saveNewArticle'])) {
            $articleModel = new ArticleModel();
            $article = $articleModel->loadFromArray($_POST);
            
            // Обработка даты публикации
            if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
                $article->publicationDate = $_POST['publicationDate'];
            } else {
                $article->publicationDate = (new \DateTime('NOW'))->format('Y-m-d');
            }
            
            // Обработка видимости
            $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
            
            // Обработка авторов
            if (isset($_POST['authors']) && is_array($_POST['authors'])) {
                $article->authors = array_map('intval', $_POST['authors']);
            } else {
                $article->authors = [];
            }
            
            $article->insert();
            
            $this->redirect(WebRouter::link("admin/adminarticle/index"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect(WebRouter::link("admin/adminarticle/index"));
        } else {
            // Показываем форму
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getList()['results'];
            
            $subcategoryModel = new SubcategoryModel();
            $subcategories = $subcategoryModel->getList()['results'];
            
            $userModel = new UserModel();
            $users = $userModel->getList()['results'];
            
            $this->view->addVar('categories', $categories);
            $this->view->addVar('subcategories', $subcategories);
            $this->view->addVar('users', $users);
            $this->view->addVar('newArticleTitle', "Новая статья");
            
            $this->view->render('admin/article/add.php');
        }
    }
    
    /**
     * Редактирование статьи
     */
    public function editAction()
    {
        $id = $_GET['id'];
        
        if (!empty($_POST['saveChanges'])) {
            $articleModel = new ArticleModel();
            $article = $articleModel->loadFromArray($_POST);
            $article->id = $id;
            
            // Обработка даты публикации
            if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
                $article->publicationDate = $_POST['publicationDate'];
            }
            
            // Обработка видимости
            $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
            
            // Обработка авторов
            if (isset($_POST['authors']) && is_array($_POST['authors'])) {
                $article->authors = array_map('intval', $_POST['authors']);
            } else {
                $article->authors = [];
            }
            
            $article->update();
            
            $this->redirect(WebRouter::link("admin/adminarticle/index&id=$id"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect(WebRouter::link("admin/adminarticle/index&id=$id"));
        } else {
            // Показываем форму
            $articleModel = new ArticleModel();
            $article = $articleModel->getById($id);
            
            if (!$article) {
                $this->redirect(WebRouter::link("admin/adminarticle/index"));
                return;
            }
            
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getList()['results'];
            
            $subcategoryModel = new SubcategoryModel();
            $subcategories = $subcategoryModel->getList()['results'];
            
            $userModel = new UserModel();
            $users = $userModel->getList()['results'];
            
            // Получаем ID авторов
            $authorIds = [];
            foreach ($article->authors as $author) {
                $authorIds[] = $author->id;
            }
            
            $this->view->addVar('article', $article);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('subcategories', $subcategories);
            $this->view->addVar('users', $users);
            $this->view->addVar('authorIds', $authorIds);
            $this->view->addVar('editArticleTitle', "Редактирование статьи");
            
            $this->view->render('admin/article/edit.php');
        }
    }
    
    /**
     * Удаление статьи
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        
        if (!empty($_POST['deleteArticle'])) {
            $articleModel = new ArticleModel();
            $article = $articleModel->loadFromArray($_POST);
            $article->id = $id;
            $article->delete();
            
            $this->redirect(WebRouter::link("admin/adminarticle/index"));
        } else {
            $articleModel = new ArticleModel();
            $article = $articleModel->getById($id);
            
            if (!$article) {
                $this->redirect(WebRouter::link("admin/adminarticle/index"));
                return;
            }
            
            $this->view->addVar('article', $article);
            $this->view->addVar('deleteArticleTitle', "Удаление статьи");
            
            $this->view->render('admin/article/delete.php');
        }
    }
    
    /**
     * Обновление видимости статьи
     */
    public function updateVisibilityAction()
    {
        $articleId = $_POST['articleId'] ?? null;
        
        if (!$articleId) {
            $this->redirect(WebRouter::link("admin/adminarticle/index"));
            return;
        }
        
        $articleModel = new ArticleModel();
        $article = $articleModel->getById($articleId);
        
        if (!$article) {
            $this->redirect(WebRouter::link("admin/adminarticle/index"));
            return;
        }
        
        $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
        $article->update();
        
        $this->redirect(WebRouter::link("admin/adminarticle/index"));
    }
}

