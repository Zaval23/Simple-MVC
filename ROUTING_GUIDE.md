# Маршрутизация в SimpleMVC. Определение действия и контроллера

Подробное руководство по маршрутизации в SimpleMVC. 
См. также [статью на сайте](http://fkn.ktu10.com/?q=node/10385) и видео-пояснение.

## Что такое маршрутизация?

**Маршрутизация (routing)** — это процесс определения того, какой код должен выполниться в ответ на HTTP-запрос пользователя. Когда пользователь запрашивает страницу сайта, системе нужно понять:
- Какой контроллер обработает запрос
- Какое действие (метод) контроллера будет вызвано

## Понятие маршрута

**Маршрут (route)** — это уникальная строка, которая соответствует конкретному действию контроллера.

**Ключевые особенности:**
- Один маршрут = одно действие одного контроллера
- Маршрут должен быть уникальным
- Маршрут можно рассматривать как "адрес действия" в системе

**Примеры маршрутов:**
- `homepage/index` → контроллер `HomepageController`, действие `indexAction`
- `admin/adminusers/index` → контроллер `AdminusersController` в namespace `admin`, действие `indexAction`
- `login/login` → контроллер `LoginController`, действие `loginAction`

## Схема работы маршрутизации

Процесс маршрутизации проходит в несколько этапов:

```
HTTP-запрос → Получение маршрута → Определение контроллера и действия → Вызов действия
```

### 1. Точка входа приложения

Все запросы обрабатываются через единую точку входа — файл `web/index.php`:

```php
<?php
require_once('web_autoload.php');

$localConfig = require(__DIR__ . '/../application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/../application/config/web.php'), 
    $localConfig
);

require_once("../application/bootstrap.php");

\ItForFree\SimpleMVC\Application::get()
    ->setConfiguration($config)
    ->run(); // ← Запуск приложения и маршрутизации
```

### 2. Получение маршрута из запроса

В методе `Application::run()` происходит получение маршрута:

```php
// Получаем строку маршрута из запроса
$route = $this->getConfigObject('core.router.class')::getRoute();
```

Метод `getRoute()` класса `WebRouter` извлекает маршрут из запроса.

### 3. Определение контроллера и действия

После получения маршрута вызывается метод `callControllerAction()`:

```php
$Router = $this->getConfigObject('core.router.class');
$Router->callControllerAction($route); // Определяем и вызываем действие контроллера
```

Этот метод:
1. Парсит строку маршрута
2. Определяет имя класса контроллера
3. Определяет имя метода действия
4. Создаёт объект контроллера
5. Вызывает метод действия

## Откуда берётся маршрут?

Маршрут может извлекаться из запроса разными способами.

### Способ 1: GET-параметр `route` (используется в SimpleMVC-example)

В SimpleMVC-example маршрут передаётся через GET-параметр:

```
http://smvc.loc/index.php?route=admin/adminusers/index
                                ↑
                            маршрут: admin/adminusers/index
```

**Как это работает:**

Метод `WebRouter::getRoute()` ищет параметр `route` в `$_GET`:

```php
public static function getRoute()
{
    return $_GET['route'] ?? 'homepage/index'; // Значение по умолчанию
}
```

### Способ 2: Маршрут как часть URL (через .htaccess)

Альтернативный способ — использовать чистые URL через mod_rewrite:

```
http://example.loc/admin/adminusers/index
                      ↑
                  маршрут: admin/adminusers/index
```

Для этого нужно настроить `.htaccess` для перенаправления всех запросов на `index.php`.

## Формат маршрута

Маршрут в SimpleMVC имеет следующий формат:

```
[namespace/]controller/action
```

### Компоненты маршрута:

1. **Namespace (опционально)** — подпапка в `application/controllers/`
   - Например: `admin` → `application/controllers/admin/`
   - Если не указан, используется корневой namespace `application\controllers`

2. **Controller** — имя контроллера без суффикса `Controller`
   - Например: `adminusers` → класс `AdminusersController`
   - Например: `homepage` → класс `HomepageController`

3. **Action** — имя действия без суффикса `Action`
   - Например: `index` → метод `indexAction()`
   - Например: `add` → метод `addAction()`

### Примеры преобразования маршрута:

| Маршрут | Namespace | Класс контроллера | Метод действия | Полный путь к файлу |
|---------|-----------|-------------------|----------------|---------------------|
| `homepage/index` | - | `HomepageController` | `indexAction()` | `application/controllers/HomepageController.php` |
| `login/login` | - | `LoginController` | `loginAction()` | `application/controllers/LoginController.php` |
| `admin/adminusers/index` | `admin` | `AdminusersController` | `indexAction()` | `application/controllers/admin/AdminusersController.php` |
| `admin/notes/add` | `admin` | `NotesController` | `addAction()` | `application/controllers/admin/NotesController.php` |

## Как маршрут преобразуется в контроллер и действие

Метод `WebRouter::callControllerAction()` выполняет преобразование:

### Алгоритм преобразования:

1. **Парсинг маршрута:**
   ```php
   // Маршрут: "admin/adminusers/index"
   $parts = explode('/', $route); 
   // Результат: ['admin', 'adminusers', 'index']
   ```

2. **Определение namespace:**
   - Если частей 3 или больше → первая часть = namespace
   - Если частей 2 → namespace = корневой (`application\controllers`)

3. **Определение имени контроллера:**
   - Берётся предпоследняя часть маршрута
   - К ней добавляется суффикс `Controller`
   - Первая буква делается заглавной
   - Пример: `adminusers` → `AdminusersController`

4. **Определение имени действия:**
   - Берётся последняя часть маршрута
   - К ней добавляется суффикс `Action`
   - Пример: `index` → `indexAction`

5. **Создание полного имени класса:**
   ```php
   // namespace + Controller
   $className = "application\\controllers\\{$namespace}\\{$controllerName}";
   // Результат: "application\controllers\admin\AdminusersController"
   ```

6. **Создание объекта и вызов метода:**
   ```php
   $controller = new $className();
   $controller->{$actionName}(); // Вызов метода
   ```

## Практические примеры

### Пример 1: Простой маршрут без namespace

**Маршрут:** `homepage/index`

**Результат:**
- Класс: `\application\controllers\HomepageController`
- Метод: `indexAction()`
- Файл: `application/controllers/HomepageController.php`

**Код контроллера:**
```php
<?php
namespace application\controllers;

class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public function indexAction()
    {
        $this->view->addVar('homepageTitle', "Домашняя страница");
        $this->view->render('homepage/index.php');
    }
}
```

### Пример 2: Маршрут с namespace

**Маршрут:** `admin/adminusers/index`

**Результат:**
- Класс: `\application\controllers\admin\AdminusersController`
- Метод: `indexAction()`
- Файл: `application/controllers/admin/AdminusersController.php`

**Код контроллера:**
```php
<?php
namespace application\controllers\admin;

class AdminusersController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public function indexAction()
    {
        $users = new \application\models\UserModel();
        $usersList = $users->getList()['results'];
        $this->view->addVar('users', $usersList);
        $this->view->render('user/index.php');
    }
}
```

### Пример 3: Маршрут с действием add

**Маршрут:** `admin/adminusers/add`

**Результат:**
- Класс: `\application\controllers\admin\AdminusersController`
- Метод: `addAction()`

**Код контроллера:**
```php
public function addAction()
{
    if (!empty($_POST['saveNewUser'])) {
        // Обработка формы создания пользователя
        $user = new \application\models\UserModel();
        $newUser = $user->loadFromArray($_POST);
        $newUser->insert();
        $this->redirect(WebRouter::link("admin/adminusers/index"));
    } else {
        // Вывод формы создания
        $this->view->render('user/add.php');
    }
}
```

## Создание ссылок на маршруты

Для создания ссылок на маршруты используется статический метод `WebRouter::link()`:

### Базовое использование:

```php
use ItForFree\SimpleMVC\Router\WebRouter;

// Создать ссылку на маршрут
$link = WebRouter::link('homepage/index');
// Результат: index.php?route=homepage/index

// Использование в HTML
echo '<a href="' . WebRouter::link('admin/adminusers/index') . '">Пользователи</a>';
```

### Использование в представлениях:

**Пример 1: Простая ссылка**
```php
<?php
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<a href="<?= WebRouter::link('homepage/index') ?>">Главная</a>
```

**Пример 2: Ссылка с параметрами**
```php
<?php
$userId = 5;
?>

<a href="<?= WebRouter::link("admin/adminusers/index&id=$userId") ?>">
    Пользователь #<?= $userId ?>
</a>
```

**Пример 3: Форма с action**
```php
<form method="post" action="<?= WebRouter::link('admin/adminusers/add') ?>">
    <!-- поля формы -->
    <button type="submit">Создать</button>
</form>
```

### Использование в контроллерах:

```php
use ItForFree\SimpleMVC\Router\WebRouter;

class MyController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public function someAction()
    {
        // Редирект на другой маршрут
        $this->redirect(WebRouter::link('homepage/index'));
    }
}
```

## Значение маршрута по умолчанию

Если маршрут не указан в запросе, используется значение по умолчанию:

```php
// В WebRouter::getRoute()
return $_GET['route'] ?? 'homepage/index'; // Значение по умолчанию
```

Это означает, что запрос к `index.php` без параметров автоматически перенаправит на главную страницу.

## Создание нового контроллера и маршрутов

### Шаг 1: Создайте класс контроллера

```php
<?php
namespace application\controllers;

class MyController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    public function indexAction()
    {
        $this->view->addVar('title', 'Моя страница');
        $this->view->render('my/index.php');
    }
    
    public function showAction()
    {
        $id = $_GET['id'] ?? null;
        // Логика действия
        $this->view->render('my/show.php');
    }
}
```

**Файл:** `application/controllers/MyController.php`

### Шаг 2: Создайте представления

**Файл:** `application/views/my/index.php`
```php
<h1><?= $title ?></h1>
```

**Файл:** `application/views/my/show.php`
```php
<h1>Просмотр элемента</h1>
```

### Шаг 3: Используйте маршруты

```php
// Маршруты:
// my/index   → MyController::indexAction()
// my/show    → MyController::showAction()

// Ссылки:
WebRouter::link('my/index');  // index.php?route=my/index
WebRouter::link('my/show');   // index.php?route=my/show
```

## Контроль доступа и маршруты

Контроллеры могут ограничивать доступ к маршрутам через свойство `$rules`:

```php
class AdminusersController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected $rules = [
        ['allow' => true, 'roles' => ['admin']],     // Разрешить админам
        ['allow' => false, 'roles' => ['?', '@']],   // Запретить остальным
    ];
    
    public function indexAction()
    {
        // Этот метод доступен только админам
    }
}
```

Подробнее см. [документацию по авторизации](docs/AuthAndAccessControl.md).

## Обработка ошибок маршрутизации

Если маршрут не найден или контроллер/действие не существует, выбрасывается исключение:

- `SmvcRoutingException` — ошибка маршрутизации
- Обрабатывается обработчиком из `core.handlers` в конфигурации

**Пример обработчика:**
```php
// application/handlers/UserExceptionHandler.php
class UserExceptionHandler
{
    public static function handle($exception)
    {
        $route = "error/"; // Маршрут страницы ошибки
        $Router = Config::getObject('core.router.class');
        $Router->callControllerAction($route, $exception);
    }
}
```

## Лучшие практики

1. **Используйте понятные имена маршрутов**: `admin/users/index` лучше, чем `a/u/i`
2. **Группируйте связанные действия**: используйте namespace для логической группировки
3. **Следуйте конвенциям именования**: Controller, Action, lowercase для маршрутов
4. **Используйте WebRouter::link()**: не создавайте ссылки вручную
5. **Обрабатывайте отсутствующие параметры**: проверяйте `$_GET['id'] ?? null`

## Отладка маршрутизации

Для отладки можно добавить логирование:

```php
// В контроллере
public function indexAction()
{
    error_log("Маршрут: " . $_GET['route']);
    error_log("Контроллер: " . get_class($this));
    error_log("Действие: indexAction");
    // ...
}
```

Или использовать встроенные инструменты отладки:

```php
// Посмотреть текущий маршрут
echo $_GET['route'] ?? 'default';

// Посмотреть доступные GET-параметры
print_r($_GET);
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10385)
- [Документация: Routing.md](docs/Routing.md)
- [Документация: Controllers.md](docs/Controllers.md)
- [Исходный код WebRouter](https://github.com/it-for-free/SimpleMVC/blob/master/src/WebRouter.php)

