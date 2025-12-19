<?php
namespace application\models;

/**
 * Модель категорий статей
 */
class CategoryModel extends \ItForFree\SimpleMVC\MVC\Model
{
    public string $tableName = 'categories';
    
    public string $orderBy = 'name ASC';
    
    public ?int $id = null;
    public $name = null;
    public $description = null;
}

