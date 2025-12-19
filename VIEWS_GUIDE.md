# Представление (View) в MVC и пример реализации в SimpleMVC

Подробное руководство по представлениям в SimpleMVC. 
См. также [статью на сайте](https://fkn.ktu10.com/?q=node/10389) и [видео-пояснение](https://youtu.be/hqDvNJ_uRpY).

## Роль представления в паттерне MVC

В паттерне **MVC (Model-View-Controller)** представление отвечает за:

- **Отображение данных** пользователю
- **Форматирование** данных (HTML, JSON, XML и т.д.)
- **Интерфейс пользователя** — визуальное представление данных

### Разделение ответственности в MVC:

- **Model (Модель)** — работа с данными (БД, файлы)
- **View (Представление)** — отображение данных
- **Controller (Контроллер)** — координация между моделью и представлением

**Важно:** Представление **НЕ должно** содержать бизнес-логику. Оно только отображает данные, переданные из контроллера.

## Что такое представление в SimpleMVC?

В SimpleMVC **представление** — это PHP-файл, который содержит смесь PHP-кода и HTML-разметки. Файлы представлений находятся в директории `application/views/`.

### Особенности представлений в SimpleMVC:

1. **Файлы с расширением `.php`** — содержат PHP-код и HTML
2. **Получают данные из контроллера** — через переменные, переданные методом `addVar()`
3. **Используют макеты (layouts)** — общая структура страницы (хедер, футер, меню)
4. **Выполняются в безопасном контексте** — переменные автоматически извлекаются через `extract()`

## Структура работы с представлениями

### Поток данных:

```
Контроллер → addVar() → View → render() → Представление → Макет → HTML → Пользователь
```

1. **Контроллер** передаёт данные через `$this->view->addVar()`
2. **View** сохраняет данные в массиве `$vars`
3. **View::render()** извлекает переменные и подключает файл представления
4. **Представление** использует переменные для генерации HTML
5. **Макет** оборачивает содержимое представления в общую структуру

## Работа с представлениями в контроллере

### Методы объекта View:

#### 1. `addVar($name, $value)` — передача переменной в представление

```php
$this->view->addVar('имяПеременной', значение);
```

**Пример:**
```php
public function indexAction()
{
    // Передать строку
    $this->view->addVar('title', 'Главная страница');
    
    // Передать массив
    $users = ['user1', 'user2', 'user3'];
    $this->view->addVar('users', $users);
    
    // Передать объект
    $user = new UserModel();
    $this->view->addVar('currentUser', $user);
}
```

**В представлении переменная будет доступна как:**
```php
<?= $title ?>        <!-- 'Главная страница' -->
<?= $users[0] ?>     <!-- 'user1' -->
<?= $currentUser->name ?>  <!-- свойство объекта -->
```

#### 2. `render($viewPath)` — рендеринг представления

```php
$this->view->render('путь/к/представлению.php');
```

**Пример:**
```php
public function indexAction()
{
    $this->view->addVar('title', 'Главная');
    $this->view->render('homepage/index.php');
}
```

**Путь к файлу:**
- Указывается относительно директории `application/views/`
- `'homepage/index.php'` → `application/views/homepage/index.php`

## Примеры использования

### Пример 1: Простое представление

**Контроллер:**
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

**Представление:** `application/views/homepage/index.php`
```php
<div class="row">
    <div class="col">
        <h1><?php echo $homepageTitle ?></h1>
    </div>
</div>
<div class="row">
    <div class="col">
        <p class="lead">Добро пожаловать в SimpleMVC!</p>
    </div>
</div>
```

**Результат:** Переменная `$homepageTitle` доступна в представлении и выводится в заголовке.

### Пример 2: Представление со списком данных

**Контроллер:**
```php
public function indexAction()
{
    $usersModel = new \application\models\UserModel();
    $users = $usersModel->getList()['results'];
    
    $this->view->addVar('users', $users);
    $this->view->render('user/index.php');
}
```

**Представление:** `application/views/user/index.php`
```php
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->id ?></td>
            <td><?= $user->login ?></td>
            <td><?= $user->email ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

**Результат:** Массив `$users` передан в представление и используется в цикле для отображения таблицы.

### Пример 3: Представление с формой

**Контроллер:**
```php
public function addAction()
{
    if (!empty($_POST)) {
        // Обработка формы
    } else {
        $this->view->addVar('pageTitle', 'Добавление пользователя');
        $this->view->render('user/add.php');
    }
}
```

**Представление:** `application/views/user/add.php`
```php
<h1><?= $pageTitle ?></h1>

<form method="post" action="<?= \ItForFree\SimpleMVC\Router\WebRouter::link('admin/adminusers/add') ?>">
    <div class="form-group">
        <label for="login">Логин</label>
        <input type="text" class="form-control" name="login" id="login">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" id="email">
    </div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
```

## Работа с переменными в представлениях

### Вывод переменных

В представлениях можно использовать несколько способов вывода:

#### 1. Эхо-тэг `<?= ?>` (рекомендуется)

```php
<?= $variable ?>
```

**Эквивалентно:**
```php
<?php echo $variable; ?>
```

#### 2. Явный `echo`

```php
<?php echo $variable; ?>
```

#### 3. Обычный текст вне PHP-тэгов

```php
<div>
    Текст и <?= $variable ?> ещё текст
</div>
```

### Безопасность: Экранирование HTML

**Важно:** При выводе пользовательских данных всегда экранируйте HTML для предотвращения XSS-атак:

```php
<!-- Плохо: уязвимо к XSS -->
<?= $userInput ?>

<!-- Хорошо: безопасно -->
<?= htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8') ?>
```

Или используйте короткую форму:
```php
<?= htmlspecialchars($userInput) ?>
```

### Условные конструкции

```php
<?php if ($user->isAllowed('admin')): ?>
    <a href="/admin">Админка</a>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= $item->name ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Нет элементов</p>
<?php endif; ?>
```

## Макеты (Layouts)

Макеты позволяют избежать дублирования общего кода (хедер, футер, меню) в каждом представлении.

### Как работают макеты:

1. Контроллер передаёт данные в представление
2. Представление рендерится и его HTML сохраняется в `$CONTENT_DATA`
3. Макет подключается и вставляет `$CONTENT_DATA` в нужное место

### Структура макета:

**Макет:** `application/views/layouts/main.php`
```php
<?php 
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>
<!DOCTYPE html>
<html>
    <?php include('includes/main/head.php'); ?>
    <body> 
        <?php include('includes/main/nav.php'); ?>
        <div class="container">
            <?= $CONTENT_DATA ?>
        </div>
        <?php include('includes/main/footer.php'); ?>
    </body>
</html>
```

**Ключевой момент:** Переменная `$CONTENT_DATA` содержит HTML, сгенерированный представлением.

### Определение макета в контроллере:

```php
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php'; // Макет для всех действий
    
    public function indexAction()
    {
        $this->view->render('homepage/index.php');
    }
}
```

## Где находятся файлы представлений

### Структура директорий:

```
application/
  views/
    layouts/              # Макеты
      main.php
      admin-main.php
      includes/          # Части макетов (head, nav, footer)
    homepage/
      index.php         # Представления для HomepageController
    login/
      index.php         # Представления для LoginController
    user/
      index.php         # Список пользователей
      add.php           # Форма добавления
      edit.php          # Форма редактирования
      view-item.php     # Просмотр одного элемента
      delete.php        # Подтверждение удаления
      includes/         # Части представлений
        admin-users-nav.php
    note/
      index.php
      add.php
      edit.php
      ...
    error.php           # Представление ошибок
```

### Путь к представлению:

При вызове:
```php
$this->view->render('homepage/index.php');
```

Фреймворк ищет файл:
```
application/views/homepage/index.php
```

Базовый путь настраивается в конфигурации:
```php
'mvc' => [
    'views' => [
        'base-template-path' => '../application/views/',
        // ...
    ]
]
```

## Использование объектов Config в представлениях

В представлениях можно получать объекты через `Config::getObject()`:

**Пример:**
```php
<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<?php if ($User->isAllowed('admin/users')): ?>
    <a href="/admin/users">Пользователи</a>
<?php endif; ?>

<p>Текущий пользователь: <?= $User->userName ?></p>
```

## Практические примеры

### Пример 1: Отображение списка с условиями

**Представление:**
```php
<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<h1>Список заметок</h1>

<?php if (empty($notes)): ?>
    <p>Заметок пока нет</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Дата</th>
                <?php if ($User->isAllowed('admin/notes/edit')): ?>
                    <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?= htmlspecialchars($note->title) ?></td>
                    <td><?= $note->publicationDate ?></td>
                    <?php if ($User->isAllowed('admin/notes/edit')): ?>
                        <td>
                            <a href="<?= WebRouter::link("admin/notes/edit&id={$note->id}") ?>">
                                Редактировать
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
```

### Пример 2: Форма редактирования

**Представление:**
```php
<h1><?= $editTitle ?></h1>

<form method="post" action="<?= WebRouter::link("admin/users/edit&id={$user->id}") ?>">
    <div class="form-group">
        <label for="login">Логин</label>
        <input type="text" 
               class="form-control" 
               name="login" 
               id="login" 
               value="<?= htmlspecialchars($user->login) ?>"
               required>
    </div>
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" 
               class="form-control" 
               name="email" 
               id="email" 
               value="<?= htmlspecialchars($user->email) ?>"
               required>
    </div>
    
    <button type="submit" name="saveChanges" class="btn btn-primary">
        Сохранить изменения
    </button>
    <button type="submit" name="cancel" class="btn btn-secondary">
        Отмена
    </button>
</form>
```

## Лучшие практики

1. **Не размещайте бизнес-логику в представлениях** — логика должна быть в контроллере или модели
2. **Всегда экранируйте пользовательские данные** — используйте `htmlspecialchars()`
3. **Используйте эхо-тэг `<?= ?>`** — более читабельно, чем `<?php echo ?>`
4. **Группируйте представления по контроллерам** — создавайте поддиректории для каждого контроллера
5. **Используйте частичные представления** — выносите повторяющиеся части в отдельные файлы
6. **Проверяйте существование переменных** — используйте `isset()` или `empty()` перед использованием
7. **Документируйте переменные** — добавляйте комментарии, какие переменные ожидает представление

## Отладка представлений

### Проверка переданных переменных:

```php
<?php
// Отладка: посмотреть все переменные
echo '<pre>';
print_r(get_defined_vars());
echo '</pre>';
?>
```

### Проверка конкретной переменной:

```php
<?php if (isset($variable)): ?>
    <?= $variable ?>
<?php else: ?>
    <p>Переменная не передана</p>
<?php endif; ?>
```

## Разница между View, Layout и Template

- **View (Представление)** — конкретный файл, отображающий данные для одного действия контроллера
- **Layout (Макет)** — общая структура страницы, оборачивающая представление (хедер, футер, меню)
- **Template (Шаблон)** — в SimpleMVC это синоним макета, используется реже

**Структура:**
```
Layout (макет)
  ├── Header (хедер)
  ├── Navigation (меню)
  ├── View (представление) ← ваши данные здесь
  └── Footer (футер)
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](https://fkn.ktu10.com/?q=node/10389)
- [Видео-пояснение](https://youtu.be/hqDvNJ_uRpY)
- [Документация: Views.md](docs/Views.md)
- [Документация: Layouts.md](docs/Layouts.md)

