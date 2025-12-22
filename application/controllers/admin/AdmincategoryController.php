<?php

namespace application\controllers\admin;

use application\models\CategoryModel;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для управления категориями в админке
 */
class AdmincategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],
        ['allow' => false, 'roles' => ['?', '@']],
    ];
    
    /**
     * Список всех категорий
     */
    public function indexAction()
    {
        $categoryModel = new CategoryModel();
        $categoriesData = $categoryModel->getList();
        
        $this->view->addVar('categories', $categoriesData['results']);
        $this->view->addVar('totalRows', $categoriesData['totalRows']);
        $this->view->addVar('listCategoriesTitle', "Список категорий");
        
        $this->view->render('admin/category/index.php');
    }
    
    /**
     * Добавление новой категории
     */
    public function addAction()
    {
        if (!empty($_POST['saveNewCategory'])) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->loadFromArray($_POST);
            $category->insert();
            
            $this->redirect(WebRouter::link("admin/admincategory/index"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect(WebRouter::link("admin/admincategory/index"));
        } else {
            $this->view->addVar('newCategoryTitle', "Новая категория");
            $this->view->render('admin/category/add.php');
        }
    }
    
    /**
     * Редактирование категории
     */
    public function editAction()
    {
        $id = $_GET['id'];
        
        if (!empty($_POST['saveChanges'])) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->loadFromArray($_POST);
            $category->id = $id;
            $category->update();
            
            $this->redirect(WebRouter::link("admin/admincategory/index"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect(WebRouter::link("admin/admincategory/index"));
        } else {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getById($id);
            
            if (!$category) {
                $this->redirect(WebRouter::link("admin/admincategory/index"));
                return;
            }
            
            $this->view->addVar('category', $category);
            $this->view->addVar('editCategoryTitle', "Редактирование категории");
            
            $this->view->render('admin/category/edit.php');
        }
    }
    
    /**
     * Удаление категории
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        
        if (!empty($_POST['deleteCategory'])) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->loadFromArray($_POST);
            $category->id = $id;
            $category->delete();
            
            $this->redirect(WebRouter::link("admin/admincategory/index"));
        } else {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getById($id);
            
            if (!$category) {
                $this->redirect(WebRouter::link("admin/admincategory/index"));
                return;
            }
            
            $this->view->addVar('category', $category);
            $this->view->addVar('deleteCategoryTitle', "Удаление категории");
            
            $this->view->render('admin/category/delete.php');
        }
    }
}

