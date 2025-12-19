# Модель в MVC и пример реализации в SimpleMVC

Подробное руководство по моделям в SimpleMVC. 
См. также [статью на сайте](http://fkn.ktu10.com/?q=node/10393) и [видео-пояснение](https://youtu.be/KxwJhhslI2U).

## Роль модели в паттерне MVC

В паттерне **MVC (Model-View-Controller)** модель отвечает за:

- **Работу с данными** — взаимодействие с базой данных, файлами, внешними API
- **Бизнес-логику** — обработка и валидация данных
- **Представление сущностей** — каждый класс модели соответствует таблице в БД или сущности

### Разделение ответственности в MVC:

- **Model (Модель)** — работа с данными и бизнес-логика
- **View (Представление)** — отображение данных
- **Controller (Контроллер)** — координация между моделью и представлением

**Важно:** Модель **НЕ должна** напрямую взаимодействовать с представлением или контроллером. Контроллер обращается к модели для получения/сохранения данных.

## Что такое модель в SimpleMVC?

В SimpleMVC **модель** — это класс PHP, который:

1. **Наследует базовый класс** `\ItForFree\SimpleMVC\MVC\Model`
2. **Соответствует таблице в БД** — указывается через свойство `$tableName`
3. **Содержит свойства** — поля таблицы (колонки)
4. **Предоставляет методы** — для работы с данными (CRUD операции)

### Структура модели:

```php
<?php
namespace application\models;

class MyModel extends \ItForFree\SimpleMVC\MVC\Model
{
    // Имя таблицы в БД (обязательно)
    public string $tableName = 'my_table';
    
    // Свойства (колонки таблицы)
    public ?int $id = null;
    public $name = null;
    public $description = null;
    
    // Дополнительные методы для работы с данными
}
```

## Базовый класс Model

Все модели наследуют базовый класс `\ItForFree\SimpleMVC\MVC\Model`, который предоставляет:

### Доступ к PDO

Базовый класс предоставляет доступ к объекту PDO через свойство `$this->pdo` для выполнения SQL-запросов.

### Методы базового класса:

#### 1. `getById($id)` — получение записи по ID

```php
$model = new MyModel();
$item = $model->getById(5); // Получить запись с id = 5
```

**Возвращает:** объект модели с заполненными свойствами или `null`, если не найдено.

**Пример:**
```php
$user = new UserModel();
$userData = $user->getById(1);
echo $userData->login; // Вывести логин пользователя
```

#### 2. `getList()` — получение списка записей

```php
$model = new MyModel();
$result = $model->getList();
// $result['results'] - массив объектов
// $result['totalRows'] - общее количество записей
```

**Возвращает:** массив с ключами:
- `results` — массив объектов модели
- `totalRows` — общее количество записей (для пагинации)

**Пример:**
```php
$users = new UserModel();
$list = $users->getList();
foreach ($list['results'] as $user) {
    echo $user->login . "\n";
}
```

#### 3. `getPage($pageNumber, $pageSize)` — получение страницы (пагинация)

```php
$model = new MyModel();
$result = $model->getPage(1, 10); // Первая страница, 10 записей на страницу
```

**Параметры:**
- `$pageNumber` — номер страницы (начиная с 1)
- `$pageSize` — количество записей на странице

**Возвращает:** тот же формат, что и `getList()`.

#### 4. `loadFromArray($array)` — создание объекта из массива

```php
$model = new MyModel();
$item = $model->loadFromArray([
    'name' => 'Название',
    'description' => 'Описание'
]);
```

**Использование:** удобно для создания объектов из данных формы (`$_POST`).

**Пример:**
```php
$user = new UserModel();
$newUser = $user->loadFromArray($_POST);
$newUser->insert(); // Сохранить в БД
```

#### 5. `delete()` — удаление записи

```php
$model = new MyModel();
$item = $model->getById(5);
$item->delete(); // Удалить запись из БД
```

**Важно:** Метод удаляет запись, соответствующую текущему объекту (по свойству `$id`).

**Пример:**
```php
$user = new UserModel();
$userToDelete = $user->getById(10);
$userToDelete->delete();
```

## Создание пользовательской модели

### Шаг 1: Создайте класс модели

```php
<?php
namespace application\models;

class ProductModel extends \ItForFree\SimpleMVC\MVC\Model
{
    // Обязательно: имя таблицы
    public string $tableName = 'products';
    
    // Свойства (колонки таблицы)
    public ?int $id = null;
    public $name = null;
    public $price = null;
    public $description = null;
    public $created_at = null;
    
    // Критерий сортировки по умолчанию (опционально)
    public string $orderBy = 'name ASC';
}
```

**Файл:** `application/models/ProductModel.php`

### Шаг 2: Используйте базовые методы

```php
// Получить продукт по ID
$product = new ProductModel();
$item = $product->getById(1);

// Получить список продуктов
$products = $product->getList();
foreach ($products['results'] as $item) {
    echo $item->name . " - " . $item->price . "\n";
}

// Создать новый продукт
$newProduct = $product->loadFromArray([
    'name' => 'Товар 1',
    'price' => 1000,
    'description' => 'Описание товара'
]);
$newProduct->insert();
```

### Шаг 3: Переопределите методы при необходимости

Если базовые методы не подходят (например, нужны JOIN'ы или сложная логика), переопределите их:

```php
public function getList()
{
    // Кастомная реализация с JOIN
    $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->tableName} p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY {$this->orderBy}";
    
    $st = $this->pdo->prepare($sql);
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
```

## Переопределение методов insert() и update()

Часто требуется переопределить методы `insert()` и `update()` для добавления специфической логики.

### Пример: Модель Note

```php
<?php
namespace application\models;

class Note extends BaseExampleModel
{
    public string $tableName = "notes";
    public string $orderBy = 'publicationDate ASC';
    
    public ?int $id = null;
    public $title = null;
    public $content = null;
    public $publicationDate = null;
    
    public function insert()
    {
        $sql = "INSERT INTO $this->tableName (title, content, publicationDate) 
                VALUES (:title, :content, :publicationDate)"; 
        $st = $this->pdo->prepare($sql);
        
        // Автоматически устанавливаем дату публикации
        $st->bindValue(":publicationDate", 
            (new \DateTime('NOW'))->format('Y-m-d H:i:s'), 
            \PDO::PARAM_STMT);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        
        $st->execute();
        $this->id = $this->pdo->lastInsertId(); // Сохраняем ID новой записи
    }
    
    public function update()
    {
        $sql = "UPDATE $this->tableName 
                SET publicationDate=:publicationDate, title=:title, content=:content 
                WHERE id = :id";  
        $st = $this->pdo->prepare($sql);
        
        $st->bindValue(":publicationDate", 
            (new \DateTime('NOW'))->format('Y-m-d H:i:s'), 
            \PDO::PARAM_STMT);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        
        $st->execute();
    }
}
```

### Пример: Модель UserModel с хешированием пароля

```php
public function insert()
{
    $sql = "INSERT INTO $this->tableName (timestamp, login, salt, pass, role, email) 
            VALUES (:timestamp, :login, :salt, :pass, :role, :email)"; 
    $st = $this->pdo->prepare($sql);
    
    $st->bindValue(":timestamp", 
        (new \DateTime('NOW'))->format('Y-m-d H:i:s'), 
        \PDO::PARAM_STMT);
    $st->bindValue(":login", $this->login, \PDO::PARAM_STR);
    
    // Генерируем соль и хешируем пароль
    $this->salt = rand(0, 1000000);
    $st->bindValue(":salt", $this->salt, \PDO::PARAM_STR);
    
    $this->pass .= $this->salt;
    $hashPass = password_hash($this->pass, PASSWORD_BCRYPT);
    $st->bindValue(":pass", $hashPass, \PDO::PARAM_STR);
    
    $st->bindValue(":role", $this->role, \PDO::PARAM_STR);
    $st->bindValue(":email", $this->email, \PDO::PARAM_STR);
    
    $st->execute();
    $this->id = $this->pdo->lastInsertId();
}
```

## Использование моделей в контроллерах

### Пример 1: Получение списка

```php
public function indexAction()
{
    $usersModel = new \application\models\UserModel();
    $users = $usersModel->getList()['results'];
    
    $this->view->addVar('users', $users);
    $this->view->render('user/index.php');
}
```

### Пример 2: Получение одной записи

```php
public function viewAction()
{
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $userModel = new \application\models\UserModel();
        $user = $userModel->getById($id);
        
        if ($user) {
            $this->view->addVar('user', $user);
            $this->view->render('user/view.php');
        } else {
            // Обработка ошибки
        }
    }
}
```

### Пример 3: Создание записи

```php
public function addAction()
{
    if (!empty($_POST)) {
        // Создаём объект из данных формы
        $userModel = new \application\models\UserModel();
        $newUser = $userModel->loadFromArray($_POST);
        
        // Сохраняем в БД
        $newUser->insert();
        
        // Редирект на список
        $this->redirect(WebRouter::link('admin/users/index'));
    } else {
        // Показываем форму
        $this->view->render('user/add.php');
    }
}
```

### Пример 4: Редактирование записи

```php
public function editAction()
{
    $id = $_GET['id'];
    
    if (!empty($_POST)) {
        // Загружаем данные из формы
        $userModel = new \application\models\UserModel();
        $user = $userModel->loadFromArray($_POST);
        $user->id = $id; // Устанавливаем ID для обновления
        
        // Обновляем в БД
        $user->update();
        
        $this->redirect(WebRouter::link("admin/users/index&id=$id"));
    } else {
        // Показываем форму с данными
        $userModel = new \application\models\UserModel();
        $user = $userModel->getById($id);
        
        $this->view->addVar('user', $user);
        $this->view->render('user/edit.php');
    }
}
```

### Пример 5: Удаление записи

```php
public function deleteAction()
{
    $id = $_GET['id'];
    
    if (!empty($_POST['confirm'])) {
        $userModel = new \application\models\UserModel();
        $user = $userModel->getById($id);
        $user->delete();
        
        $this->redirect(WebRouter::link('admin/users/index'));
    } else {
        // Показываем форму подтверждения
        $userModel = new \application\models\UserModel();
        $user = $userModel->getById($id);
        
        $this->view->addVar('user', $user);
        $this->view->render('user/delete.php');
    }
}
```

## Свойства модели

### Обязательные свойства:

#### `$tableName` — имя таблицы в БД

```php
public string $tableName = 'users';
```

**Важно:** Это свойство должно быть обязательно переопределено в каждой модели.

### Опциональные свойства:

#### `$orderBy` — критерий сортировки по умолчанию

```php
public string $orderBy = 'login ASC';
// или
public string $orderBy = 'created_at DESC';
```

Используется методами `getList()` и `getPage()` для сортировки результатов.

#### Свойства-колонки таблицы

```php
public ?int $id = null;        // Первичный ключ
public $name = null;           // Строковое поле
public $price = null;          // Числовое поле
public $description = null;    // Текстовое поле
public $created_at = null;     // Дата/время
```

**Рекомендация:** Используйте типизацию (`?int`, `string`) для лучшей читаемости кода.

## Дополнительные методы

Модель может содержать собственные методы для специфической логики:

### Пример: Поиск пользователя по логину

```php
class UserModel extends \ItForFree\SimpleMVC\MVC\Model
{
    // ...
    
    public function findByLogin($login)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE login = :login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":login", $login, \PDO::PARAM_STR);
        $st->execute();
        
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $user = clone $this;
            foreach ($row as $key => $value) {
                $user->$key = $value;
            }
            return $user;
        }
        
        return null;
    }
}
```

### Пример: Подсчёт записей

```php
public function count()
{
    $sql = "SELECT COUNT(*) as total FROM {$this->tableName}";
    $st = $this->pdo->query($sql);
    $row = $st->fetch(\PDO::FETCH_ASSOC);
    return (int)$row['total'];
}
```

## Промежуточные базовые классы

Можно создавать промежуточные базовые классы для переиспользования общей логики:

### Пример: BaseExampleModel

```php
<?php
namespace application\models;

class BaseExampleModel extends \ItForFree\SimpleMVC\MVC\Model
{
    public function likesUpper($id, $tableName)
    {
        $modelData = $this->getById($id, $tableName);
        $modelData->likes++;
        $modelData->update();
    }
    
    public function getModelLikes($id, $tableName)
    {
        $modelData = $this->getById($id, $tableName);
        return $modelData->likes;
    }
}
```

Затем другие модели могут наследоваться от него:

```php
class Note extends BaseExampleModel
{
    public string $tableName = "notes";
    // ...
}
```

## Лучшие практики

1. **Одна модель = одна таблица** — каждая модель соответствует одной таблице БД
2. **Используйте PDO Prepared Statements** — для безопасности от SQL-инъекций
3. **Переопределяйте методы при необходимости** — не бойтесь переопределять базовые методы
4. **Валидируйте данные** — добавляйте валидацию в методы `insert()` и `update()`
5. **Используйте типизацию** — указывайте типы для свойств
6. **Документируйте методы** — добавляйте PHPDoc комментарии
7. **Обрабатывайте ошибки** — проверяйте результаты операций
8. **Используйте транзакции** — для сложных операций с несколькими таблицами

## Безопасность

### Защита от SQL-инъекций

**Всегда используйте подготовленные запросы (Prepared Statements):**

```php
// ❌ ПЛОХО: уязвимо к SQL-инъекциям
$sql = "SELECT * FROM users WHERE login = '{$_POST['login']}'";

// ✅ ХОРОШО: безопасно
$sql = "SELECT * FROM users WHERE login = :login";
$st = $this->pdo->prepare($sql);
$st->bindValue(":login", $_POST['login'], \PDO::PARAM_STR);
$st->execute();
```

### Валидация данных

```php
public function insert()
{
    // Валидация
    if (empty($this->name)) {
        throw new \Exception("Имя обязательно для заполнения");
    }
    
    if ($this->price < 0) {
        throw new \Exception("Цена не может быть отрицательной");
    }
    
    // SQL запрос
    $sql = "INSERT INTO ...";
    // ...
}
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10393)
- [Видео-пояснение](https://youtu.be/KxwJhhslI2U)
- [Документация: Models.md](docs/Models.md)
- [Исходный код базового класса Model](https://github.com/it-for-free/SimpleMVC/blob/master/src/mvc/Model.php)

