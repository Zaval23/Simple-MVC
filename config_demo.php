<?php
/**
 * Демонстрация работы с конфигурацией в SimpleMVC
 * 
 * Запуск: php config_demo.php
 */

require_once 'console_autoload.php';

use ItForFree\SimpleMVC\Config;

echo "=== Демонстрация работы с конфигурацией SimpleMVC ===\n\n";

// Загружаем конфигурацию (как это делается в web/index.php)
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);

// Передаём конфигурацию в приложение
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Получение простых значений из конфигурации:\n";
echo "   core.db.dns: " . Config::get('core.db.dns') . "\n";
echo "   core.db.username: " . Config::get('core.db.username') . "\n";
echo "   core.mvc.views.base-template-path: " . Config::get('core.mvc.views.base-template-path') . "\n\n";

echo "2. Получение объектов через Config::getObject():\n";

try {
    // Получаем объект пользователя (singleton)
    $User = Config::getObject('core.user.class');
    echo "   ✓ Объект User получен: " . get_class($User) . "\n";
    
    // Получаем объект роутера
    $router = Config::getObject('core.router.class');
    echo "   ✓ Объект Router получен: " . get_class($router) . "\n";
    
    // Получаем объект сессии
    $session = Config::getObject('core.session.class');
    echo "   ✓ Объект Session получен: " . get_class($session) . "\n\n";
} catch (Exception $e) {
    echo "   ✗ Ошибка: " . $e->getMessage() . "\n\n";
}

echo "3. Демонстрация слияния конфигураций:\n";
echo "   Основной конфиг (web.php): dbname=dbname\n";
echo "   Локальный конфиг (web-local.php): dbname=smvcbase\n";
echo "   После слияния используется: " . Config::get('core.db.dns') . "\n";
echo "   (Локальный конфиг переопределил значение из основного)\n\n";

echo "4. Структура конфигурации:\n";
echo "   - core.db - настройки базы данных\n";
echo "   - core.router - настройки роутера\n";
echo "   - core.mvc.views - пути к представлениям\n";
echo "   - core.user - класс пользователя (с DI)\n";
echo "   - core.session - класс сессии\n";
echo "   - core.handlers - обработчики исключений\n\n";

echo "5. Инъекция зависимостей (DI):\n";
echo "   Компонент 'user' в конфиге имеет:\n";
echo "     construct: { session: '@session', router: '@router' }\n";
echo "   Это означает, что при создании объекта AuthUser\n";
echo "   автоматически передаются объекты session и router\n";
echo "   через их псевдонимы (@session, @router)\n\n";

echo "=== Демонстрация завершена ===\n";

