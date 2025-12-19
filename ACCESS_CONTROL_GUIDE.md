# Реализация контроля доступа в SimpleMVC

Подробное руководство по контролю доступа (Authorization) в SimpleMVC. 
См. также [статью на сайте](http://fkn.ktu10.com/?q=node/10395) и [видео-пояснение](https://youtu.be/rjcA71JZgf0).

## Что такое контроль доступа?

**Контроль доступа (Access Control / Authorization)** — это механизм, который определяет, какие действия может выполнять пользователь в системе в зависимости от его роли и прав.

### Разница между аутентификацией и авторизацией:

- **Аутентификация (Authentication)** — проверка подлинности ("Кто вы?")
- **Авторизация (Authorization)** — проверка прав ("Что вы можете делать?")

**Пример:**
- Пользователь вошёл в систему (аутентификация) ✅
- Может ли он редактировать записи? (авторизация) ❓

## Как работает контроль доступа в SimpleMVC

### Компоненты системы контроля доступа:

1. **Роли пользователей** — определяют права доступа
2. **Правила доступа ($rules)** — определяются в контроллерах
3. **Методы проверки** — `isAllowed()`, `returnIfAllowed()`
4. **Автоматическая проверка** — ядро проверяет доступ перед выполнением действий

### Схема работы:

```
Запрос → Контроллер → Проверка $rules → Проверка роли → Разрешить/Запретить
```

## Роли пользователей

### Стандартные роли:

1. **`'guest'`** (или `'?'`) — неавторизованный пользователь (гость)
2. **`'@'`** — любой авторизованный пользователь
3. **`'admin'`** — администратор
4. **`'auth_user'`** — обычный авторизованный пользователь

### Псевдонимы ролей:

- `'?'` — синоним `'guest'` (неавторизованный)
- `'@'` — любой авторизованный пользователь

## Правила доступа в контроллерах

### Определение правил

Правила доступа определяются в контроллере через свойство `$rules`:

```php
class MyController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],      // Разрешить админам
        ['allow' => false, 'roles' => ['?', '@']],    // Запретить остальным
    ];
    
    public function indexAction()
    {
        // Код действия
    }
}
```

### Синтаксис правил:

```php
protected array $rules = [
    [
        'allow' => true,              // true = разрешить, false = запретить
        'roles' => ['role1', 'role2'], // Массив ролей
        'actions' => ['action1']      // Опционально: конкретные действия
    ],
    // Можно указать несколько правил
];
```

**Параметры:**
- `allow` — `true` (разрешить) или `false` (запретить)
- `roles` — массив ролей, к которым применяется правило
- `actions` — опционально: массив конкретных действий (если не указано, правило применяется ко всем действиям)

### Как применяются правила:

1. Правила проверяются **сверху вниз**
2. Первое **подходящее** правило определяет результат
3. Если правило подходит по роли, применяется его `allow`
4. Если ни одно правило не подошло, доступ **запрещён** по умолчанию

## Примеры правил доступа

### Пример 1: Только для админов

```php
class AdminController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],      // Разрешить админам
        ['allow' => false, 'roles' => ['?', '@']],    // Запретить остальным
    ];
}
```

**Логика:**
- Админы → ✅ Доступ разрешён
- Остальные → ❌ Доступ запрещён

### Пример 2: Только для авторизованных пользователей

```php
class PrivateController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        ['allow' => true, 'roles' => ['@']],      // Разрешить авторизованным
        ['allow' => false, 'roles' => ['?']],     // Запретить гостям
    ];
}
```

**Логика:**
- Авторизованные → ✅ Доступ разрешён
- Гости → ❌ Доступ запрещён

### Пример 3: Для всех

```php
class PublicController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@']], // Разрешить всем
    ];
}
```

**Логика:**
- Все пользователи → ✅ Доступ разрешён

### Пример 4: Разные правила для разных действий

```php
class LoginController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        // Страница входа — только для гостей
        ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],
        // Выход — только для авторизованных
        ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],
    ];
    
    public function loginAction()
    {
        // Доступно только гостям
    }
    
    public function logoutAction()
    {
        // Доступно только авторизованным
    }
}
```

### Пример 5: Сложная логика

```php
class ComplexController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        // Админы могут всё
        ['allow' => true, 'roles' => ['admin']],
        // Модераторы могут просматривать и редактировать
        ['allow' => true, 'roles' => ['moderator'], 'actions' => ['index', 'edit']],
        // Обычные пользователи могут только просматривать
        ['allow' => true, 'roles' => ['auth_user'], 'actions' => ['index']],
        // Гости — запрещено
        ['allow' => false, 'roles' => ['?']],
    ];
}
```

## Реальный пример: AdminusersController

```php
<?php
namespace application\controllers\admin;

class AdminusersController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],      // Разрешить админам
        ['allow' => false, 'roles' => ['?', '@']],    // Запретить остальным
    ];
    
    public function indexAction()
    {
        // Только админы могут получить доступ к этому методу
        $users = new \application\models\UserModel();
        $usersList = $users->getList()['results'];
        $this->view->addVar('users', $usersList);
        $this->view->render('user/index.php');
    }
}
```

**Что происходит:**
1. Пользователь запрашивает маршрут `admin/adminusers/index`
2. Создаётся объект `AdminusersController`
3. Ядро проверяет правила доступа из `$rules`
4. Если пользователь — админ → доступ разрешён, выполняется `indexAction()`
5. Если пользователь не админ → выбрасывается исключение `SmvcAccessException`

## Проверка доступа в коде

### Метод isAllowed()

Проверяет, имеет ли пользователь доступ к маршруту:

```php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');

if ($User->isAllowed('admin/users/index')) {
    // Пользователь имеет доступ
    echo "Доступ разрешён";
} else {
    // Доступ запрещён
    echo "Доступ запрещён";
}
```

**Возвращает:** `true` если доступ разрешён, `false` если запрещён.

### Использование в представлениях

```php
<?php
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<?php if ($User->isAllowed('admin/users/index')): ?>
    <a href="<?= WebRouter::link('admin/users/index') ?>">
        Управление пользователями
    </a>
<?php endif; ?>

<?php if ($User->isAllowed('login/logout')): ?>
    <a href="<?= WebRouter::link('login/logout') ?>">
        Выход (<?= $User->userName ?>)
    </a>
<?php endif; ?>
```

### Метод returnIfAllowed()

Удобный метод для условного вывода контента:

```php
$User = Config::getObject('core.user.class');

// Если доступ есть — вернёт строку, если нет — пустую строку
echo $User->returnIfAllowed('admin/users/edit', '<a href="/edit">Редактировать</a>');
```

**Синтаксис:**
```php
returnIfAllowed($route, $contentIfAllowed)
```

**Возвращает:**
- `$contentIfAllowed` — если доступ разрешён
- Пустую строку `''` — если доступ запрещён

### Примеры использования returnIfAllowed()

#### В таблице со списком

```php
<?php
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<table>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->login ?></td>
            <td><?= $user->email ?></td>
            <td>
                <?= $User->returnIfAllowed(
                    "admin/users/edit",
                    '<a href="' . WebRouter::link("admin/users/edit&id={$user->id}") . '">Редактировать</a>'
                ) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
```

#### В карточке элемента

```php
<h2><?= $note->title ?></h2>
<p><?= $note->content ?></p>

<span>
    <?= $User->returnIfAllowed(
        "admin/notes/edit",
        '<a href="' . WebRouter::link("admin/notes/edit&id={$note->id}") . '">[Редактировать]</a>'
    ) ?>
    
    <?= $User->returnIfAllowed(
        "admin/notes/delete",
        '<a href="' . WebRouter::link("admin/notes/delete&id={$note->id}") . '">[Удалить]</a>'
    ) ?>
</span>
```

### Метод explainAccess()

Для отладки можно получить подробную информацию о проверке доступа:

```php
$User = Config::getObject('core.user.class');

$explanation = $User->explainAccess('admin/users/index');
print_r($explanation);
```

**Использование:** Полезно для отладки и понимания, почему доступ разрешён или запрещён.

## Обработка ошибок доступа

### Исключение SmvcAccessException

Если пользователь не имеет доступа, выбрасывается исключение `SmvcAccessException`.

### Обработчик исключений

В конфигурации можно указать обработчик:

```php
'handlers' => [
    'ItForFree\SimpleMVC\Exceptions\SmvcAccessException' 
        => \application\handlers\UserExceptionHandler::class,
]
```

**Пример обработчика:**

```php
<?php
namespace application\handlers;

use ItForFree\SimpleMVC\Config;

class UserExceptionHandler
{
    public static function handle($exception)
    {
        // Редирект на страницу ошибки или логина
        $route = "error/";
        $Router = Config::getObject('core.router.class');
        $Router->callControllerAction($route, $exception);
    }
}
```

## Лучшие практики

### 1. Принцип наименьших привилегий

✅ **Хорошо:** Разрешать доступ только тем, кому он действительно нужен
```php
protected array $rules = [
    ['allow' => true, 'roles' => ['admin']],  // Только админы
    ['allow' => false, 'roles' => ['?', '@']], // Остальным запрещено
];
```

❌ **Плохо:** Разрешать всем, а потом запрещать
```php
protected array $rules = [
    ['allow' => true, 'roles' => ['?', '@']], // Всем разрешено
    ['allow' => false, 'roles' => ['admin']],  // Админам запрещено (неправильно!)
];
```

### 2. Явное указание правил

✅ **Хорошо:** Явно указывать, что разрешено и что запрещено
```php
protected array $rules = [
    ['allow' => true, 'roles' => ['admin']],
    ['allow' => false, 'roles' => ['?', '@']],
];
```

### 3. Группировка правил по логике

✅ **Хорошо:** Группировать связанные правила
```php
protected array $rules = [
    // Публичные действия
    ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['index', 'view']],
    // Действия для авторизованных
    ['allow' => true, 'roles' => ['@'], 'actions' => ['create', 'edit']],
    // Действия только для админов
    ['allow' => true, 'roles' => ['admin'], 'actions' => ['delete', 'moderate']],
];
```

### 4. Использование isAllowed() в представлениях

✅ **Хорошо:** Проверять доступ перед показом элементов интерфейса
```php
<?php if ($User->isAllowed('admin/users')): ?>
    <a href="/admin/users">Управление пользователями</a>
<?php endif; ?>
```

### 5. Использование returnIfAllowed() для коротких фрагментов

✅ **Хорошо:** Использовать для простых условий
```php
<?= $User->returnIfAllowed('admin/edit', '<a href="/edit">Редактировать</a>') ?>
```

## Полный пример: Система с несколькими уровнями доступа

### Контроллер с разными уровнями доступа

```php
<?php
namespace application\controllers\admin;

class ContentController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        // Админы могут всё
        ['allow' => true, 'roles' => ['admin']],
        
        // Модераторы могут просматривать и редактировать
        ['allow' => true, 'roles' => ['moderator'], 'actions' => ['index', 'view', 'edit']],
        
        // Авторизованные пользователи могут только просматривать
        ['allow' => true, 'roles' => ['auth_user'], 'actions' => ['index', 'view']],
        
        // Гостям запрещено
        ['allow' => false, 'roles' => ['?']],
    ];
    
    public function indexAction()
    {
        // Доступ: админы, модераторы, авторизованные
    }
    
    public function viewAction()
    {
        // Доступ: админы, модераторы, авторизованные
    }
    
    public function editAction()
    {
        // Доступ: админы, модераторы
    }
    
    public function deleteAction()
    {
        // Доступ: только админы
    }
}
```

### Представление с условным выводом

```php
<?php
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>

<h1>Список статей</h1>

<?php if (!empty($articles)): ?>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Дата</th>
                <?php if ($User->isAllowed('admin/content/edit')): ?>
                    <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td>
                        <a href="<?= WebRouter::link("admin/content/view&id={$article->id}") ?>">
                            <?= htmlspecialchars($article->title) ?>
                        </a>
                    </td>
                    <td><?= $article->publicationDate ?></td>
                    <?php if ($User->isAllowed('admin/content/edit')): ?>
                        <td>
                            <?= $User->returnIfAllowed(
                                'admin/content/edit',
                                '<a href="' . WebRouter::link("admin/content/edit&id={$article->id}") . '">Редактировать</a>'
                            ) ?>
                            
                            <?= $User->returnIfAllowed(
                                'admin/content/delete',
                                '<a href="' . WebRouter::link("admin/content/delete&id={$article->id}") . '">Удалить</a>'
                            ) ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
```

## Отладка контроля доступа

### Проверка текущей роли

```php
$User = Config::getObject('core.user.class');

echo "Текущий пользователь: " . $User->userName . "\n";
echo "Роль: " . $User->role . "\n";
echo "Гость: " . ($User->userName === 'guest' ? 'Да' : 'Нет') . "\n";
```

### Детальная информация о доступе

```php
$User = Config::getObject('core.user.class');

$explanation = $User->explainAccess('admin/users/index');
print_r($explanation);
```

Это выведет подробную информацию о том, почему доступ разрешён или запрещён.

## Типичные сценарии

### Сценарий 1: Публичная страница + админ-панель

```php
class PageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    // Публичные действия — для всех
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['index', 'view']],
        // Админские действия — только для админов
        ['allow' => true, 'roles' => ['admin'], 'actions' => ['admin', 'moderate']],
    ];
}
```

### Сценарий 2: Личный кабинет

```php
class ProfileController extends \ItForFree\SimpleMVC\MVC\Controller
{
    // Только для авторизованных
    protected array $rules = [
        ['allow' => true, 'roles' => ['@']],
        ['allow' => false, 'roles' => ['?']],
    ];
}
```

### Сценарий 3: API с разными уровнями доступа

```php
class ApiController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        // Публичные endpoints
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['publicData']],
        // Защищённые endpoints
        ['allow' => true, 'roles' => ['@'], 'actions' => ['userData', 'updateProfile']],
        // Админские endpoints
        ['allow' => true, 'roles' => ['admin'], 'actions' => ['adminData', 'deleteUser']],
    ];
}
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10395)
- [Видео-пояснение](https://youtu.be/rjcA71JZgf0)
- [Документация: AuthAndAccessControl.md](docs/AuthAndAccessControl.md)
- [Руководство по авторизации](AUTHENTICATION_GUIDE.md)

