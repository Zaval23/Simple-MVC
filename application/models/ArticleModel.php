<?php
namespace application\models;

/**
 * Модель статей
 */
class ArticleModel extends \ItForFree\SimpleMVC\MVC\Model
{
    public string $tableName = 'articles';
    
    public string $orderBy = 'publicationDate DESC';
    
    public ?int $id = null;
    public $publicationDate = null;
    public ?int $categoryId = null;
    public ?int $subcategoryId = null;
    public $title = null;
    public $summary = null;
    public $content = null;
    public int $is_visible = 1;
    
    /**
     * @var array Массив ID авторов статьи
     */
    public $authors = [];
    
    /**
     * Получить список статей (совместимый с базовым классом)
     */
    public function getList(int $numRows = 1000000): array
    {
        return $this->getListFiltered(null, null, $numRows, 'publicationDate DESC');
    }
    
    /**
     * Получить список статей с фильтрацией (только видимые для публичной части)
     * 
     * @param int|null $categoryId ID категории
     * @param int|null $subcategoryId ID подкатегории
     * @param int $numRows Количество строк
     * @param string $order Сортировка
     * @return array
     */
    public function getListFiltered(?int $categoryId = null, ?int $subcategoryId = null, int $numRows = 1000000, string $order = 'publicationDate DESC'): array
    {
        $fromPart = "FROM {$this->tableName}";
        $whereClauses = [];
        $params = [];
        
        // Фильтр по категории
        if ($categoryId) {
            $whereClauses[] = "categoryId = :categoryId";
            $params[':categoryId'] = $categoryId;
        }
        
        // Фильтр по подкатегории
        if ($subcategoryId) {
            $whereClauses[] = "subcategoryId = :subcategoryId";
            $params[':subcategoryId'] = $subcategoryId;
        }
        
        // Только видимые статьи
        $whereClauses[] = "is_visible = 1";
        
        $whereClause = "";
        if (!empty($whereClauses)) {
            $whereClause = "WHERE " . implode(" AND ", $whereClauses);
        }
        
        $sql = "SELECT * $fromPart $whereClause ORDER BY $order LIMIT :numRows";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $list = [];
        
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $item = clone $this;
            foreach ($row as $key => $value) {
                $item->$key = $value;
            }
            $item->loadAuthors();
            $list[] = $item;
        }
        
        // Получаем общее количество
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        $st->execute();
        $totalRows = $st->fetch(\PDO::FETCH_ASSOC);
        
        return ['results' => $list, 'totalRows' => $totalRows['totalRows']];
    }
    
    /**
     * Получить список статей для админки (без фильтрации по видимости)
     * 
     * @param int|null $categoryId ID категории
     * @param int|null $subcategoryId ID подкатегории
     * @param int $numRows Количество строк
     * @param string $order Сортировка
     * @return array
     */
    public function getListForAdmin($categoryId = null, $subcategoryId = null, $numRows = 1000000, $order = 'publicationDate DESC')
    {
        $fromPart = "FROM {$this->tableName}";
        $whereClauses = [];
        $params = [];
        
        if ($categoryId) {
            $whereClauses[] = "categoryId = :categoryId";
            $params[':categoryId'] = $categoryId;
        }
        
        if ($subcategoryId) {
            $whereClauses[] = "subcategoryId = :subcategoryId";
            $params[':subcategoryId'] = $subcategoryId;
        }
        
        $whereClause = "";
        if (!empty($whereClauses)) {
            $whereClause = "WHERE " . implode(" AND ", $whereClauses);
        }
        
        $sql = "SELECT * $fromPart $whereClause ORDER BY $order LIMIT :numRows";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $list = [];
        
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $item = clone $this;
            foreach ($row as $key => $value) {
                $item->$key = $value;
            }
            $item->loadAuthors();
            $list[] = $item;
        }
        
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        $st->execute();
        $totalRows = $st->fetch(\PDO::FETCH_ASSOC);
        
        return ['results' => $list, 'totalRows' => $totalRows['totalRows']];
    }
    
    /**
     * Переопределяем getById для загрузки авторов
     */
    public function getById(int $id, string $tableName = ''): ?\ItForFree\SimpleMVC\MVC\Model
    {
        $item = parent::getById($id, $tableName);
        if ($item) {
            $item->loadAuthors();
        }
        return $item;
    }
    
    /**
     * Вставка новой статьи
     */
    public function insert()
    {
        // Устанавливаем дату публикации если не указана
        if (empty($this->publicationDate)) {
            $this->publicationDate = (new \DateTime('NOW'))->format('Y-m-d');
        }
        
        $sql = "INSERT INTO {$this->tableName} (publicationDate, categoryId, subcategoryId, title, summary, content, is_visible) 
                VALUES (:publicationDate, :categoryId, :subcategoryId, :title, :summary, :content, :is_visible)";
        $st = $this->pdo->prepare($sql);
        
        $st->bindValue(":publicationDate", $this->publicationDate, \PDO::PARAM_STR);
        $st->bindValue(":categoryId", $this->categoryId, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, $this->subcategoryId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":is_visible", $this->is_visible, \PDO::PARAM_INT);
        
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
        
        // Сохраняем авторов после создания статьи
        if (!empty($this->authors)) {
            $this->setAuthors($this->authors);
        }
    }
    
    /**
     * Обновление статьи
     */
    public function update()
    {
        if (empty($this->publicationDate)) {
            $this->publicationDate = (new \DateTime('NOW'))->format('Y-m-d');
        }
        
        $sql = "UPDATE {$this->tableName} SET publicationDate=:publicationDate, categoryId=:categoryId, 
                subcategoryId=:subcategoryId, title=:title, summary=:summary, content=:content, is_visible=:is_visible 
                WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        
        $st->bindValue(":publicationDate", $this->publicationDate, \PDO::PARAM_STR);
        $st->bindValue(":categoryId", $this->categoryId, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, $this->subcategoryId ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":is_visible", $this->is_visible, \PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        
        $st->execute();
        
        // Обновляем авторов
        if (isset($this->authors)) {
            $this->setAuthors($this->authors);
        }
    }
    
    /**
     * Получить список авторов статьи
     * 
     * @return array Массив объектов UserModel
     */
    public function getAuthors()
    {
        if (is_null($this->id)) {
            return [];
        }
        
        $sql = "SELECT u.* FROM users u
                INNER JOIN article_authors aa ON u.id = aa.userId
                WHERE aa.articleId = :articleId
                ORDER BY u.login ASC";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
        $st->execute();
        
        $authors = [];
        $userModel = new UserModel();
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $user = clone $userModel;
            foreach ($row as $key => $value) {
                $user->$key = $value;
            }
            $authors[] = $user;
        }
        
        return $authors;
    }
    
    /**
     * Установить авторов для статьи
     * 
     * @param array $authorIds Массив ID пользователей
     */
    public function setAuthors($authorIds)
    {
        if (is_null($this->id)) {
            return;
        }
        
        if (!is_array($authorIds)) {
            $authorIds = [];
        }
        
        $authorIds = array_map('intval', $authorIds);
        $authorIds = array_filter($authorIds);
        
        // Удаляем все существующие связи
        $sql = "DELETE FROM article_authors WHERE articleId = :articleId";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
        $st->execute();
        
        // Добавляем новые связи
        if (!empty($authorIds)) {
            $sql = "INSERT INTO article_authors (articleId, userId) VALUES (:articleId, :userId)";
            $st = $this->pdo->prepare($sql);
            
            foreach ($authorIds as $userId) {
                $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
                $st->bindValue(":userId", $userId, \PDO::PARAM_INT);
                $st->execute();
            }
        }
    }
    
    /**
     * Загрузить авторов для статьи
     */
    public function loadAuthors()
    {
        $this->authors = $this->getAuthors();
    }
}

