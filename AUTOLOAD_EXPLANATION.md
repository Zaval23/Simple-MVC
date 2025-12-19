# Автозагрузка классов в PHP на примере SimpleMVC

Этот документ объясняет, как работает автозагрузка классов в проекте SimpleMVC. 
Подробнее см. [статью на сайте](http://fkn.ktu10.com/?q=node/10383) и видео-пояснение.

## Что такое автозагрузка классов?

**Автозагрузка классов (Autoloading)** — это механизм в PHP, который позволяет автоматически подключать файлы с классами в момент их первого использования, без необходимости вручную писать `require` или `include` для каждого файла.

### Зачем это нужно?

Без автозагрузки нам пришлось бы писать:

```php
require_once 'application/controllers/HomepageController.php';
require_once 'application/models/Note.php';
require_once 'application/models/UserModel.php';
// ... и так для каждого класса
```

С автозагрузкой достаточно просто использовать класс:

```php
$controller = new \application\controllers\HomepageController();
$note = new \application\models\Note();
// PHP автоматически найдёт и подключит нужные файлы
```

## Как работает автозагрузка в SimpleMVC

В проекте SimpleMVC используется **двухуровневая система автозагрузки**:

1. **Собственная автозагрузка** для классов приложения (контроллеры, модели и т.д.)
2. **Автозагрузка Composer** для классов из пакетов (ядро SimpleMVC, библиотеки)

### 1. Точка входа и подключение автозагрузки

Все начинается с файла `web/index.php`:

```php
<?php

require_once('web_autoload.php'); // ← Здесь подключается автозагрузка

// ... остальной код
```

### 2. Структура файлов автозагрузки

Проект использует несколько файлов автозагрузки:

#### `base_autoload.php` — базовая функция автозагрузки

```php
<?php

function baseAutoload($className, $baseDir) {
    $className = ltrim($className, '\\'); // Убираем начальный обратный слэш
    $fileName = '';
    $fileName .= $baseDir;
    $namespace = '';

    // Если есть namespace (например, application\controllers)
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos); // application\controllers
        $className = substr($className, $lastNsPos + 1); // HomepageController
        // Преобразуем обратные слэши в прямые (для путей файлов)
        $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    // Преобразуем подчёркивания в разделители директорий (PSR-0 style)
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    
    require $fileName; // Подключаем файл
}
```

**Как это работает:**
- Класс `application\controllers\HomepageController` → путь `application/controllers/HomepageController.php`
- Класс `application\models\Note` → путь `application/models/Note.php`

#### `web/web_autoload.php` — автозагрузка для веб-приложения

```php
<?php
use ItForFree\rusphp\File\Path;

require_once '../base_autoload.php';

function autoload($className)
{
    $baseDir = Path::addToDocumentRoot('..' . DIRECTORY_SEPARATOR);
    baseAutoload($className, $baseDir);
}

// Регистрируем функцию автозагрузки
spl_autoload_register('autoload'); 

// Подключаем автозагрузку Composer (для пакетов из vendor/)
require_once __DIR__ . '/../vendor/autoload.php';
```

**Важные моменты:**
1. Использует базовую функцию `baseAutoload()` с базовой директорией проекта
2. Регистрирует функцию через `spl_autoload_register()` — PHP будет вызывать её, когда встретит неопределённый класс
3. Подключает автозагрузку Composer для библиотек из `vendor/`

#### `console_autoload.php` — автозагрузка для консольных команд

Аналогична `web_autoload.php`, но для консольного приложения:

```php
<?php

require_once 'base_autoload.php';

function autoload($className) {
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;
    baseAutoload($className, $baseDir);
}

spl_autoload_register('autoload');
require_once __DIR__ . '/vendor/autoload.php';
```

### 3. Автозагрузка Composer

Composer генерирует файл `vendor/autoload.php`, который автоматически подключает классы из всех установленных пакетов (ядро SimpleMVC, библиотеки).

**Как это работает:**

1. В `composer.json` указаны зависимости:
```json
{
    "require": {
        "it-for-free/simple-mvc": "dev-master",
        "it-for-free/rusphp": "v2.*"
    }
}
```

2. После `composer install` создаётся `vendor/autoload.php`

3. Этот файл использует стандарт **PSR-4** для автозагрузки классов по namespace

**Пример:**
- Класс `\ItForFree\SimpleMVC\MVC\Controller` → файл из пакета `simple-mvc`
- Класс `\ItForFree\rusphp\File\Path` → файл из пакета `rusphp`

## Примеры работы автозагрузки

### Пример 1: Использование контроллера

```php
// В файле web/index.php после require_once('web_autoload.php')

// Когда ядро пытается создать контроллер:
$controller = new \application\controllers\HomepageController();

// PHP видит, что класс не определён и вызывает зарегистрированную функцию autoload()
// autoload() преобразует namespace в путь: application/controllers/HomepageController.php
// И подключает файл через require
```

### Пример 2: Использование модели

```php
// В контроллере
$note = new \application\models\Note();

// Автозагрузка найдёт файл: application/models/Note.php
// И автоматически подключит его
```

### Пример 3: Использование класса из ядра

```php
// В коде приложения
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');

// Автозагрузка Composer найдёт класс в vendor/it-for-free/simple-mvc/
// И автоматически подключит его
```

## Структура namespace в SimpleMVC

### Классы приложения (собственная автозагрузка):

- `application\controllers\*` → `application/controllers/*.php`
- `application\models\*` → `application/models/*.php`
- `application\handlers\*` → `application/handlers/*.php`
- `application\assets\*` → `application/assets/*.php`

### Классы ядра (автозагрузка Composer):

- `ItForFree\SimpleMVC\*` → из пакета `simple-mvc` в `vendor/`
- `ItForFree\rusphp\*` → из пакета `rusphp` в `vendor/`

## Важные функции PHP для автозагрузки

### `spl_autoload_register()`

Регистрирует функцию для автозагрузки классов:

```php
spl_autoload_register('autoload');
// или
spl_autoload_register(function($className) {
    // логика автозагрузки
});
```

**Особенности:**
- Можно зарегистрировать несколько функций автозагрузки
- PHP будет вызывать их по порядку, пока класс не будет найден
- Если ни одна функция не нашла класс, вызовется ошибка

### `__autoload()` (устарело)

Старый способ автозагрузки (используйте `spl_autoload_register()` вместо него).

## Порядок работы автозагрузки

Когда PHP встречает неопределённый класс:

1. Вызывается первая зарегистрированная функция (`autoload()` из `web_autoload.php`)
2. Она пытается найти класс в структуре проекта приложения
3. Если не найдено, вызывается следующая функция (автозагрузка Composer)
4. Composer ищет класс в установленных пакетах по PSR-4
5. Если класс найден — файл подключается
6. Если не найден — генерируется ошибка "Class not found"

## Преимущества автозагрузки

1. **Не нужно вручную подключать файлы** — PHP делает это автоматически
2. **Удобство работы с namespace** — классы организованы по структуре
3. **Соответствие стандартам** — используется PSR-4 для пакетов
4. **Производительность** — файлы подключаются только когда нужны (lazy loading)

## Отладка автозагрузки

Если класс не находится, можно включить отладку в `base_autoload.php`:

```php
function baseAutoload($className, $baseDir) {
    // ... существующий код ...
    
    // Раскомментируйте для отладки:
    echo "Ищем класс: $className\n";
    echo "Путь: $fileName\n";
    echo "Файл существует: " . (file_exists($fileName) ? 'да' : 'нет') . "\n";
    
    require $fileName;
}
```

## Полезные ссылки

- [Статья на сайте fkn.ktu10.com](http://fkn.ktu10.com/?q=node/10383)
- [PSR-4 Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
- [Документация PHP: spl_autoload_register()](https://www.php.net/manual/ru/function.spl-autoload-register.php)
- [Composer: Autoloading](https://getcomposer.org/doc/01-basic-usage.md#autoloading)

