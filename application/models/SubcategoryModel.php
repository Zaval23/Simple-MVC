<?php
namespace application\models;

/**
 * Модель подкатегорий статей
 */
class SubcategoryModel extends \ItForFree\SimpleMVC\MVC\Model
{
    public string $tableName = 'subcategories';
    
    public string $orderBy = 'name ASC';
    
    public ?int $id = null;
    public $name = null;
    public ?int $categoryId = null;
    
    /**
     * Получить список подкатегорий по ID категории
     * 
     * @param int $categoryId ID категории
     * @return array
     */
    public function getListByCategory($categoryId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE categoryId = :categoryId ORDER BY {$this->orderBy}";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        
        $list = [];
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $item = clone $this;
            foreach ($row as $key => $value) {
                $item->$key = $value;
            }
            $list[] = $item;
        }
        
        return ['results' => $list, 'totalRows' => count($list)];
    }
}

