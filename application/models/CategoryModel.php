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
    
    /**
     * Вставка новой категории
     */
    public function insert(): void
    {
        $sql = "INSERT INTO {$this->tableName} (name, description) VALUES (:name, :description)";
        $st = $this->pdo->prepare($sql);
        
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    /**
     * Обновление категории
     */
    public function update(): void
    {
        $sql = "UPDATE {$this->tableName} SET name = :name, description = :description WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        
        $st->execute();
    }
}

