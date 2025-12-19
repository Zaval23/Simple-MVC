# Реализация Авторизации пользователя в SimpleMVC

Подробное руководство по авторизации и аутентификации в SimpleMVC. 
См. также [статью на сайте](http://fkn.ktu10.com/?q=node/10394) и [видео-пояснение](https://youtu.be/slmXwmZkpKM).

## Основные понятия

### Аутентификация (Authentication)

**Аутентификация** — процесс проверки подлинности пользователя (логин и пароль). 
Отвечает на вопрос: "Кто вы?" / "Вы действительно тот, за кого себя выдаёте?"

### Авторизация (Authorization)

**Авторизация** — процесс проверки прав доступа к ресурсам. 
Отвечает на вопрос: "Что вы можете делать?" / "Имеете ли вы право на это действие?"

## Как работает авторизация в SimpleMVC

### Архитектура авторизации:

1. **Сессии** — хранение информации о текущем пользователе
2. **Класс AuthUser** — управление авторизацией (наследуется от базового `User`)
3. **Роли пользователей** — определение прав доступа
4. **Контроль доступа** — правила в контроллерах

### Схема работы:

```
Пользователь → Форма входа → LoginController → AuthUser::login() → Проверка БД → Сессия → Авторизация
```

## Компоненты системы авторизации

### 1. Класс AuthUser

Класс `AuthUser` наследуется от базового класса `\ItForFree\SimpleMVC\User` и реализует методы проверки данных:

**Файл:** `application/models/AuthUser.php`

```php
<?php
namespace application\models;

use ItForFree\SimpleMVC\User;

class AuthUser extends User
{        
    /**
     * Проверка логина и пароля пользователя.
     */
    protected function checkAuthData($login, $pass): bool {
        $result = false;
        $User = new UserModel();
        $siteAuthData = $User->getAuthData($login);	
        
        if (isset($siteAuthData['pass'])) {
            // Добавляем соль к паролю
            $pass .= $siteAuthData['salt'];
            // Проверяем пароль
            $passForCheck = password_verify($pass, $siteAuthData['pass']);
            if ($passForCheck) {
                $result = true;
            }
        }	
        return $result;
    }

    /**
     * Получить роль по имени пользователя
     */
    protected function getRoleByUserName($login): string {
        $User = new UserModel();
        $siteAuthData = $User->getRole($login);
        if (isset($siteAuthData['role'])) {
            return $siteAuthData['role'];
        }
    }
}
```

**Важные методы:**
- `checkAuthData($login, $pass)` — проверка логина и пароля
- `getRoleByUserName($login)` — получение роли пользователя

### 2. Конфигурация

В `application/config/web.php` настраивается класс пользователя:

```php
'user' => [
    'class' => \application\models\AuthUser::class,
    'construct' => [
        'session' => '@session',  // Инъекция сессии
        'router' => '@router'     // Инъекция роутера
    ]
],
'session' => [
    'class' => ItForFree\SimpleMVC\Session::class,
    'alias' => '@session'
]
```

### 3. Работа с сессиями

SimpleMVC использует класс `Session` для работы с сессиями PHP (`$_SESSION`).

**Сессия хранит:**
- Логин пользователя
- Роль пользователя
- Другие данные авторизации

## Получение объекта пользователя

Для работы с текущим пользователем используется объект, полученный через конфигурацию:

```php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
```

**Важно:** Это singleton-объект, при каждом вызове возвращается один и тот же экземпляр.

## Авторизация: вход и выход

### Вход в систему (login)

#### Контроллер LoginController

```php
<?php
namespace application\controllers;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

class LoginController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],  // Гости могут входить
        ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']], // Авторизованные могут выходить
    ];
    
    public function loginAction()
    {
        if (!empty($_POST)) {
            // Обработка формы входа
            $login = $_POST['userName'];
            $pass = $_POST['password'];
            
            // Получаем объект пользователя
            $User = Config::getObject('core.user.class');
            
            // Попытка авторизации
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
}
```

#### Представление формы входа

**Файл:** `application/views/login/index.php`

```php
<h2><?= $loginTitle ?></h2>

<form method="post" action="<?= \ItForFree\SimpleMVC\Router\WebRouter::link('login/login') ?>">
    <?php if (!empty($_GET['auth'])): ?>
        <div class="alert alert-danger">
            Неверное имя пользователя или пароль
        </div>
    <?php endif; ?>
    
    <div class="form-group">
        <label for="userName">Введите имя пользователя</label>
        <input type="text" class="form-control" id="userName" name="userName" required>
    </div>
    
    <div class="form-group">
        <label for="password">Введите пароль</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    
    <input type="submit" class="btn btn-primary" name="login" value="Войти">
</form>
```

#### Как работает метод login()

Метод `login($login, $pass)` (из базового класса `User`):

1. Вызывает `checkAuthData($login, $pass)` для проверки данных
2. Если данные верны, вызывает `getRoleByUserName($login)` для получения роли
3. Сохраняет данные в сессию (логин, роль)
4. Возвращает `true` при успехе, `false` при ошибке

### Выход из системы (logout)

```php
public function logoutAction()
{
    $User = Config::getObject('core.user.class');
    $User->logout();
    $this->redirect(WebRouter::link("login/login"));
}
```

Метод `logout()`:
- Очищает данные сессии
- Устанавливает роль пользователя в 'guest'
- Удаляет информацию об авторизации

## Хранение паролей в БД

### Безопасное хранение паролей

Пароли **никогда** не хранятся в открытом виде. Используется:

1. **Соль (salt)** — случайное число, добавляемое к паролю
2. **Хеширование** — использование функции `password_hash()` с алгоритмом bcrypt

### Пример сохранения пароля (UserModel::insert())

```php
public function insert()
{
    // ... другие поля ...
    
    // Генерируем случайную соль
    $this->salt = rand(0, 1000000);
    
    // Добавляем соль к паролю
    $this->pass .= $this->salt;
    
    // Хешируем пароль с солью
    $hashPass = password_hash($this->pass, PASSWORD_BCRYPT);
    
    $st->bindValue(":salt", $this->salt, \PDO::PARAM_STR);
    $st->bindValue(":pass", $hashPass, \PDO::PARAM_STR);
    
    // ... выполнение запроса ...
}
```

### Пример проверки пароля (AuthUser::checkAuthData())

```php
protected function checkAuthData($login, $pass): bool
{
    $result = false;
    $User = new UserModel();
    $siteAuthData = $User->getAuthData($login); // Получаем соль и хеш из БД
    
    if (isset($siteAuthData['pass'])) {
        // Добавляем соль к введённому паролю
        $pass .= $siteAuthData['salt'];
        
        // Проверяем пароль
        $passForCheck = password_verify($pass, $siteAuthData['pass']);
        if ($passForCheck) {
            $result = true;
        }
    }
    return $result;
}
```

## Работа с текущим пользователем

### Получение информации о пользователе

```php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');

// Логин пользователя
$login = $User->userName;

// Роль пользователя
$role = $User->role;

// Проверка, авторизован ли пользователь
$isGuest = ($User->userName === 'guest');
```

### Проверка доступа к маршруту

```php
$User = Config::getObject('core.user.class');

// Проверка доступа
if ($User->isAllowed('admin/users/index')) {
    // Пользователь имеет доступ
    echo "Доступ разрешён";
} else {
    // Доступ запрещён
    echo "Доступ запрещён";
}
```

### Условный вывод в представлениях

```php
<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<?php if ($User->isAllowed('admin/users')): ?>
    <a href="/admin/users">Управление пользователями</a>
<?php endif; ?>

<?php if ($User->userName !== 'guest'): ?>
    <p>Добро пожаловать, <?= $User->userName ?>!</p>
<?php else: ?>
    <a href="/login">Войти</a>
<?php endif; ?>
```

## Контроль доступа в контроллерах

### Правила доступа ($rules)

Контроллеры могут ограничивать доступ через свойство `$rules`:

```php
class AdminusersController extends \ItForFree\SimpleMVC\MVC\Controller
{
    protected array $rules = [
        ['allow' => true, 'roles' => ['admin']],      // Разрешить админам
        ['allow' => false, 'roles' => ['?', '@']],    // Запретить остальным
    ];
    
    public function indexAction()
    {
        // Этот метод доступен только админам
    }
}
```

### Синтаксис правил:

```php
protected array $rules = [
    ['allow' => true/false, 'roles' => ['роль1', 'роль2']],
    // ...
];
```

**Параметры:**
- `allow` — `true` (разрешить) или `false` (запретить)
- `roles` — массив ролей
  - `'admin'` — роль администратора
  - `'@'` — авторизованный пользователь (любая роль)
  - `'?'` — гость (не авторизован)

### Примеры правил:

#### Пример 1: Только для админов

```php
protected array $rules = [
    ['allow' => true, 'roles' => ['admin']],
    ['allow' => false, 'roles' => ['?', '@']],
];
```

#### Пример 2: Только для авторизованных

```php
protected array $rules = [
    ['allow' => true, 'roles' => ['@']],
    ['allow' => false, 'roles' => ['?']],
];
```

#### Пример 3: Для всех

```php
protected array $rules = [
    ['allow' => true, 'roles' => ['?', '@']],
];
```

#### Пример 4: Разные правила для разных действий

```php
protected array $rules = [
    // Для действия login — только гости
    ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],
    // Для действия logout — только авторизованные
    ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],
];
```

## Роли пользователей

### Стандартные роли:

1. **`'guest'`** — неавторизованный пользователь (гость)
2. **`'@'`** — любой авторизованный пользователь
3. **`'admin'`** — администратор
4. **`'auth_user'`** — обычный авторизованный пользователь

### Определение роли в БД

Роль хранится в таблице `users` в поле `role`:

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(30) NOT NULL,
    pass VARCHAR(250) NOT NULL,
    salt INT NOT NULL,
    role VARCHAR(25) NOT NULL,  -- Роль пользователя
    email VARCHAR(40) NOT NULL,
    timestamp DATE NOT NULL
);
```

**Примеры значений:**
- `'admin'` — администратор
- `'auth_user'` — обычный пользователь

### Получение роли

Роль получается из БД в методе `getRoleByUserName()`:

```php
protected function getRoleByUserName($login): string {
    $User = new UserModel();
    $siteAuthData = $User->getRole($login);
    if (isset($siteAuthData['role'])) {
        return $siteAuthData['role'];
    }
    return 'guest'; // По умолчанию
}
```

## Работа с сессиями

### Что хранится в сессии

После успешной авторизации в сессии сохраняются:
- Логин пользователя (`userName`)
- Роль пользователя (`role`)
- Другие данные (если необходимо)

### Использование сессии в коде

```php
// Получение объекта сессии
$session = Config::getObject('core.session.class');

// Установка значения
$session->set('key', 'value');

// Получение значения
$value = $session->get('key');

// Удаление значения
$session->remove('key');
```

**Важно:** Обычно работа с сессией происходит внутри класса `User`, вам не нужно делать это напрямую.

## Полный пример: создание системы авторизации

### Шаг 1: Структура таблицы users

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(30) NOT NULL UNIQUE,
    pass VARCHAR(250) NOT NULL,
    salt INT NOT NULL,
    role VARCHAR(25) NOT NULL DEFAULT 'auth_user',
    email VARCHAR(40) NOT NULL,
    timestamp DATE NOT NULL
);
```

### Шаг 2: Модель UserModel

```php
<?php
namespace application\models;

class UserModel extends \ItForFree\SimpleMVC\MVC\Model
{
    public string $tableName = 'users';
    public ?int $id = null;
    public $login = null;
    public $pass = null;
    protected $role = null;
    public $email = null;
    public $salt = null;
    
    public function getAuthData($login): ?array {
        $sql = "SELECT salt, pass FROM users WHERE login = :login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":login", $login, \PDO::PARAM_STR);
        $st->execute();
        $authData = $st->fetch();
        return $authData ? $authData : null;
    }
    
    public function getRole($login): array {
        $sql = "SELECT role FROM users WHERE login = :login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":login", $login, \PDO::PARAM_STR);
        $st->execute();
        return $st->fetch();
    }
}
```

### Шаг 3: Класс AuthUser

```php
<?php
namespace application\models;

use ItForFree\SimpleMVC\User;

class AuthUser extends User
{
    protected function checkAuthData($login, $pass): bool {
        $result = false;
        $User = new UserModel();
        $siteAuthData = $User->getAuthData($login);
        
        if (isset($siteAuthData['pass'])) {
            $pass .= $siteAuthData['salt'];
            $passForCheck = password_verify($pass, $siteAuthData['pass']);
            if ($passForCheck) {
                $result = true;
            }
        }
        return $result;
    }

    protected function getRoleByUserName($login): string {
        $User = new UserModel();
        $siteAuthData = $User->getRole($login);
        if (isset($siteAuthData['role'])) {
            return $siteAuthData['role'];
        }
        return 'guest';
    }
}
```

### Шаг 4: Контроллер LoginController

```php
<?php
namespace application\controllers;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;

class LoginController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],
        ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],
    ];
    
    public function loginAction()
    {
        if (!empty($_POST)) {
            $login = $_POST['userName'];
            $pass = $_POST['password'];
            $User = Config::getObject('core.user.class');
            
            if($User->login($login, $pass)) {
                $this->redirect(WebRouter::link("homepage/index"));
            } else {
                $this->redirect(WebRouter::link("login/login&auth=deny"));
            }
        } else {
            $this->view->addVar('loginTitle', "Вход в систему");
            $this->view->render('login/index.php');
        }
    }
    
    public function logoutAction()
    {
        $User = Config::getObject('core.user.class');
        $User->logout();
        $this->redirect(WebRouter::link("login/login"));
    }
}
```

### Шаг 5: Представление

```php
<h2><?= $loginTitle ?></h2>

<?php if (!empty($_GET['auth'])): ?>
    <div class="alert alert-danger">Неверное имя пользователя или пароль</div>
<?php endif; ?>

<form method="post" action="<?= WebRouter::link('login/login') ?>">
    <div class="form-group">
        <label for="userName">Имя пользователя</label>
        <input type="text" class="form-control" id="userName" name="userName" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Войти</button>
</form>
```

## Безопасность

### 1. Хеширование паролей

✅ **Всегда** используйте `password_hash()` и `password_verify()`:

```php
// Сохранение
$hash = password_hash($password . $salt, PASSWORD_BCRYPT);

// Проверка
$isValid = password_verify($password . $salt, $hash);
```

### 2. Использование соли

✅ **Всегда** добавляйте уникальную соль к каждому паролю перед хешированием.

### 3. Защита от SQL-инъекций

✅ **Всегда** используйте подготовленные запросы (Prepared Statements):

```php
$sql = "SELECT * FROM users WHERE login = :login";
$st = $this->pdo->prepare($sql);
$st->bindValue(":login", $login, \PDO::PARAM_STR);
```

### 4. Защита сессий

- Используйте HTTPS для передачи данных
- Устанавливайте безопасные cookie параметры
- Регулярно обновляйте ID сессии

### 5. Защита от брутфорса

- Ограничивайте количество попыток входа
- Используйте CAPTCHA
- Блокируйте IP после множественных неудачных попыток

## Отладка авторизации

### Проверка текущего пользователя

```php
$User = Config::getObject('core.user.class');

// Информация о пользователе
echo "Логин: " . $User->userName . "\n";
echo "Роль: " . $User->role . "\n";
echo "Гость: " . ($User->userName === 'guest' ? 'Да' : 'Нет') . "\n";
```

### Проверка доступа

```php
$User = Config::getObject('core.user.class');

// Детальная информация о доступе
$explanation = $User->explainAccess('admin/users/index');
print_r($explanation);
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10394)
- [Видео-пояснение](https://youtu.be/slmXwmZkpKM)
- [Документация: AuthAndAccessControl.md](docs/AuthAndAccessControl.md)
- [Видео о контроле доступа](https://youtu.be/rjcA71JZgf0)

