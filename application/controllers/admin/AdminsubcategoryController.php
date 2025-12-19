<?php

namespace application\controllers\admin;

use application\models\SubcategoryModel;
use application\models\CategoryModel;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

/**
 * Контроллер для управления подкатегориями в админке
 */
class AdminsubcategoryController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],
        ['allow' => false, 'roles' => ['?', '@']],
    ];
    
    /**
     * Список всех подкатегорий
     */
    public function indexAction()
    {
        $subcategoryModel = new SubcategoryModel();
        $subcategoriesData = $subcategoryModel->getList();
        
        $categoryModel = new CategoryModel();
        $categoriesData = $categoryModel->getList();
        $categories = [];
        foreach ($categoriesData['results'] as $cat) {
            $categories[$cat->id] = $cat;
        }
        
        $this->view->addVar('subcategories', $subcategoriesData['results']);
        $this->view->addVar('totalRows', $subcategoriesData['totalRows']);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('listSubcategoriesTitle', "Список подкатегорий");
        
        $this->view->render('admin/subcategory/index.php');
    }
    
    /**
     * Добавление новой подкатегории
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST['saveNewSubcategory'])) {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->loadFromArray($_POST);
            $subcategory->insert();
            
            $this->redirect($Url::link("admin/adminsubcategory/index"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect($Url::link("admin/adminsubcategory/index"));
        } else {
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getList()['results'];
            
            $this->view->addVar('categories', $categories);
            $this->view->addVar('newSubcategoryTitle', "Новая подкатегория");
            
            $this->view->render('admin/subcategory/add.php');
        }
    }
    
    /**
     * Редактирование подкатегории
     */
    public function editAction()
    {
        $id = $_GET['id'];
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST['saveChanges'])) {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->loadFromArray($_POST);
            $subcategory->id = $id;
            $subcategory->update();
            
            $this->redirect($Url::link("admin/adminsubcategory/index"));
        } elseif (!empty($_POST['cancel'])) {
            $this->redirect($Url::link("admin/adminsubcategory/index"));
        } else {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->getById($id);
            
            if (!$subcategory) {
                $this->redirect($Url::link("admin/adminsubcategory/index"));
                return;
            }
            
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getList()['results'];
            
            $this->view->addVar('subcategory', $subcategory);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('editSubcategoryTitle', "Редактирование подкатегории");
            
            $this->view->render('admin/subcategory/edit.php');
        }
    }
    
    /**
     * Удаление подкатегории
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST['deleteSubcategory'])) {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->loadFromArray($_POST);
            $subcategory->id = $id;
            $subcategory->delete();
            
            $this->redirect($Url::link("admin/adminsubcategory/index"));
        } else {
            $subcategoryModel = new SubcategoryModel();
            $subcategory = $subcategoryModel->getById($id);
            
            if (!$subcategory) {
                $this->redirect($Url::link("admin/adminsubcategory/index"));
                return;
            }
            
            $this->view->addVar('subcategory', $subcategory);
            $this->view->addVar('deleteSubcategoryTitle', "Удаление подкатегории");
            
            $this->view->render('admin/subcategory/delete.php');
        }
    }
}

