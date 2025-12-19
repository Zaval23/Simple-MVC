# Роль Контроллера в MVC и создание объектов из конфига

Подробное руководство по контроллерам в SimpleMVC и работе с объектами через конфигурацию.
См. также [статью на сайте](http://fkn.ktu10.com/?q=node/10388) и [видео-пояснение](https://youtu.be/vGE8o99faYM).

## Роль контроллера в паттерне MVC

В паттерне **MVC (Model-View-Controller)** контроллер играет центральную роль:

- **Model (Модель)** — работа с данными (БД, файлы и т.д.)
- **View (Представление)** — отображение данных пользователю
- **Controller (Контроллер)** — координация между моделью и представлением, обработка запросов пользователя

### Что делает контроллер:

1. **Обрабатывает HTTP-запросы** — получает данные от пользователя (`$_GET`, `$_POST`)
2. **Взаимодействует с моделями** — получает/сохраняет данные через модели
3. **Подготавливает данные для представления** — передаёт данные в view
4. **Принимает решения** — решает, какое представление показать, куда сделать редирект и т.д.

### Схема работы MVC:

```
Пользователь → HTTP-запрос → Контроллер → Модель → Данные → Контроллер → Представление → HTML → Пользователь
                ↑                                                                         ↓
                └─────────────────────── Редирект/JSON ───────────────────────────────────┘
```

## Контроллеры в SimpleMVC

### Базовое определение:

**Контроллер** — это класс, унаследованный от `\ItForFree\SimpleMVC\MVC\Controller`

**Действие контроллера (Action)** — метод класса, обрабатывающий конкретный запрос (оканчивается на `Action`)

### Пример контроллера:

```php
<?php
namespace application\controllers;

class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    public function indexAction()
    {
        // Обработка запроса
        $this->view->addVar('title', 'Главная страница');
        $this->view->render('homepage/index.php');
    }
}
```

## Базовый класс Controller

Все контроллеры наследуют базовый класс `\ItForFree\SimpleMVC\MVC\Controller`, который предоставляет:

### Основные свойства:

#### 1. `$view` — объект для работы с представлениями

Автоматически создаётся в конструкторе базового класса:

```php
public function __construct() {
    $this->view = new View($this->layoutPath);
}
```

**Использование:**
```php
// Передать переменную в представление
$this->view->addVar('title', 'Заголовок');

// Отрендерить представление
$this->view->render('homepage/index.php');
```

#### 2. `$layoutPath` — путь к макету

Задаётся в каждом контроллере:

```php
public string $layoutPath = 'main.php'; // Основной макет
// или
public string $layoutPath = 'admin-main.php'; // Админский макет
```

**Важно:** Это свойство используется базовым классом при создании объекта `$view`.

### Основные методы:

#### `redirect($path)` — метод редиректа

```php
public function redirect($path) {
    header("Location: $path");
    exit; // Остановка выполнения скрипта
}
```

**Использование:**
```php
// Редирект на другой маршрут
$this->redirect(WebRouter::link('homepage/index'));

// Редирект на внешний URL
$this->redirect('https://example.com');
```

## Получение объектов из конфигурации (Singleton)

В контроллерах часто нужно получить доступ к объектам системы (пользователь, роутер и т.д.). Для этого используется метод `Config::getObject()`.

### Что такое Singleton?

**Singleton (Одиночка)** — паттерн проектирования, гарантирующий, что класс имеет только один экземпляр, и предоставляющий глобальную точку доступа к этому экземпляру.

**Преимущества:**
- Гарантирует единственный экземпляр объекта
- Глобальный доступ к объекту
- Экономия памяти (не создаётся множество объектов)

### Использование Config::getObject()

Метод `Config::getObject()` возвращает **singleton-объект** — при каждом вызове возвращается один и тот же экземпляр.

**Синтаксис:**
```php
use ItForFree\SimpleMVC\Config;

$object = Config::getObject('путь.к.классу.в.конфиге');
```

### Основные объекты, доступные через Config:

#### 1. Объект пользователя (`core.user.class`)

```php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
```

**Использование:**
```php
// Проверка авторизации
if ($User->login($login, $password)) {
    // Пользователь авторизован
}

// Выход
$User->logout();

// Проверка доступа
if ($User->isAllowed('admin/users/index')) {
    // Пользователь имеет доступ
}

// Получить имя пользователя
$username = $User->userName;
```

**Пример из LoginController:**
```php
public function loginAction()
{
    if (!empty($_POST)) {
        $login = $_POST['userName'];
        $pass = $_POST['password'];
        $User = Config::getObject('core.user.class'); // Получаем объект пользователя
        
        if($User->login($login, $pass)) {
            $this->redirect(WebRouter::link("homepage/index"));
        } else {
            $this->redirect(WebRouter::link("login/login&auth=deny"));
        }
    }
}
```

#### 2. Объект роутера (`core.router.class`)

```php
use ItForFree\SimpleMVC\Config;

$router = Config::getObject('core.router.class');
```

**Использование:**
```php
// Создать ссылку (статический метод)
$link = WebRouter::link('admin/users/index');

// Или через объект
$router = Config::getObject('core.router.class');
// Обычно используется статический метод WebRouter::link()
```

#### 3. Объект сессии (`core.session.class`)

```php
use ItForFree\SimpleMVC\Config;

$session = Config::getObject('core.session.class');
```

### Важно: Singleton поведение

При каждом вызове `Config::getObject()` возвращается **один и тот же экземпляр**:

```php
$User1 = Config::getObject('core.user.class');
$User2 = Config::getObject('core.user.class');

// $User1 и $User2 — это один и тот же объект
// Изменения в $User1 отразятся в $User2
```

**Пример:**
```php
$User = Config::getObject('core.user.class');
$User->login('admin', 'password');

// В другом месте кода
$User2 = Config::getObject('core.user.class');
echo $User2->userName; // 'admin' — данные сохранены
```

## Структура контроллера

### Типичная структура контроллера:

```php
<?php
namespace application\controllers;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

class MyController extends \ItForFree\SimpleMVC\MVC\Controller
{
    // 1. Свойство макета (обязательно)
    public string $layoutPath = 'main.php';
    
    // 2. Правила доступа (опционально)
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],
    ];
    
    // 3. Пользовательские свойства (опционально)
    public $pageTitle = "Моя страница";
    
    // 4. Действия контроллера
    public function indexAction()
    {
        // Получение объектов через Config
        $User = Config::getObject('core.user.class');
        
        // Работа с моделями
        $model = new \application\models\MyModel();
        $data = $model->getList();
        
        // Передача данных в представление
        $this->view->addVar('data', $data);
        $this->view->addVar('pageTitle', $this->pageTitle);
        
        // Рендеринг представления
        $this->view->render('my/index.php');
    }
    
    public function createAction()
    {
        if (!empty($_POST)) {
            // Обработка формы
            $model = new \application\models\MyModel();
            $item = $model->loadFromArray($_POST);
            $item->insert();
            
            // Редирект после создания
            $this->redirect(WebRouter::link('my/index'));
        } else {
            // Показ формы
            $this->view->render('my/create.php');
        }
    }
}
```

## Примеры использования контроллеров

### Пример 1: Простой контроллер (HomepageController)

```php
<?php
namespace application\controllers;

class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    public $homepageTitle = "Домашняя страница";
    
    public function indexAction()
    {
        $this->view->addVar('homepageTitle', $this->homepageTitle);
        $this->view->render('homepage/index.php');
    }
}
```

**Что происходит:**
1. Пользователь запрашивает маршрут `homepage/index`
2. Вызывается `HomepageController::indexAction()`
3. Метод передаёт переменную в представление
4. Рендерится представление `homepage/index.php`

### Пример 2: Контроллер с авторизацией (LoginController)

```php
<?php
namespace application\controllers;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

class LoginController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?'], 'actions' => ['login']], // Гости могут входить
        ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']], // Авторизованные могут выходить
    ];
    
    public function loginAction()
    {
        if (!empty($_POST)) {
            // Обработка формы входа
            $login = $_POST['userName'];
            $pass = $_POST['password'];
            
            // Получаем объект пользователя (singleton)
            $User = Config::getObject('core.user.class');
            
            if($User->login($login, $pass)) {
                // Успешная авторизация
                $this->redirect(WebRouter::link("homepage/index"));
            } else {
                // Ошибка авторизации
                $this->redirect(WebRouter::link("login/login&auth=deny"));
            }
        } else {
            // Показ формы входа
            $this->view->addVar('loginTitle', "Вход в систему");
            $this->view->render('login/index.php');
        }
    }
    
    public function logoutAction()
    {
        // Получаем объект пользователя
        $User = Config::getObject('core.user.class');
        $User->logout();
        $this->redirect(WebRouter::link("login/login"));
    }
}
```

**Что происходит:**
1. При входе получаем объект пользователя через `Config::getObject()`
2. Используем метод `login()` для авторизации
3. Делаем редирект в зависимости от результата
4. При выходе вызываем `logout()` и редиректим на страницу входа

### Пример 3: Контроллер с работой с моделью

```php
<?php
namespace application\controllers\admin;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

class AdminusersController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],
        ['allow' => false, 'roles' => ['?', '@']],
    ];
    
    public function indexAction()
    {
        // Получаем данные через модель
        $usersModel = new \application\models\UserModel();
        $users = $usersModel->getList()['results'];
        
        // Передаём данные в представление
        $this->view->addVar('users', $users);
        $this->view->render('user/index.php');
    }
    
    public function addAction()
    {
        if (!empty($_POST['saveNewUser'])) {
            // Создание нового пользователя
            $userModel = new \application\models\UserModel();
            $newUser = $userModel->loadFromArray($_POST);
            $newUser->insert();
            
            // Редирект на список
            $this->redirect(WebRouter::link("admin/adminusers/index"));
        } else {
            // Показ формы создания
            $this->view->render('user/add.php');
        }
    }
}
```

## Жизненный цикл выполнения контроллера

1. **Маршрутизация** → определяется контроллер и действие
2. **Создание объекта контроллера** → вызывается конструктор базового класса
3. **Инициализация `$view`** → создаётся объект View с макетом из `$layoutPath`
4. **Проверка доступа** → проверяются правила из `$rules`
5. **Вызов действия** → выполняется метод действия (например, `indexAction()`)
6. **Работа с данными** → взаимодействие с моделями, получение объектов через Config
7. **Подготовка представления** → передача данных через `$this->view->addVar()`
8. **Рендеринг** → вызов `$this->view->render()`

## Работа с объектами через Config в разных контекстах

### В контроллере:

```php
public function someAction()
{
    $User = Config::getObject('core.user.class');
    // Использование объекта
}
```

### В представлении:

```php
<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<?php if ($User->isAllowed('admin/users')): ?>
    <a href="/admin/users">Пользователи</a>
<?php endif; ?>
```

### В обработчике исключений:

```php
class UserExceptionHandler
{
    public static function handle($exception)
    {
        $Router = Config::getObject('core.router.class');
        $Router->callControllerAction('error/', $exception);
    }
}
```

## Рекомендации по созданию контроллеров

1. **Один контроллер = одна сущность** — группируйте связанные действия
2. **Используйте понятные имена действий** — `index`, `create`, `edit`, `delete`
3. **Разделяйте GET и POST** — проверяйте `$_POST` для обработки форм
4. **Делайте редиректы после изменений** — после POST-запросов делайте редирект (PRG-паттерн)
5. **Используйте Config::getObject()** — для получения системных объектов
6. **Определяйте правила доступа** — используйте `$rules` для контроля доступа
7. **Не смешивайте логику** — контроллер координирует, но не содержит бизнес-логику

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10388)
- [Видео-пояснение](https://youtu.be/vGE8o99faYM)
- [Документация: Controllers.md](docs/Controllers.md)
- [Рекомендации по созданию контроллеров](http://fkn.ktu10.com/?q=node/10717)
- [Документация: Config.md](docs/Config.md) — работа с конфигурацией

