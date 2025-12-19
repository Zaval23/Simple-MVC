<?php
/**
 * Демонстрация работы контроллеров и создания объектов из конфига
 * 
 * Запуск: php controllers_demo.php
 */

require_once 'console_autoload.php';

use ItForFree\SimpleMVC\Config;

echo "=== Демонстрация контроллеров и объектов из конфига ===\n\n";

// Загружаем конфигурацию
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Роль контроллера в MVC:\n";
echo "   Контроллер — это класс, который:\n";
echo "   - Обрабатывает HTTP-запросы\n";
echo "   - Взаимодействует с моделями (данные)\n";
echo "   - Координирует работу с представлениями (view)\n";
echo "   - Принимает решения (редиректы, валидация и т.д.)\n\n";

echo "2. Структура контроллера в SimpleMVC:\n\n";
echo "   class MyController extends \\ItForFree\\SimpleMVC\\MVC\\Controller\n";
echo "   {\n";
echo "       public string \$layoutPath = 'main.php';  // Макет\n";
echo "       protected array \$rules = [...];           // Правила доступа\n";
echo "       \n";
echo "       public function indexAction()              // Действие\n";
echo "       {\n";
echo "           // Код действия\n";
echo "       }\n";
echo "   }\n\n";

echo "3. Базовый класс Controller предоставляет:\n";
echo "   - \$this->view — объект для работы с представлениями\n";
echo "   - \$layoutPath — путь к макету (определяется в контроллере)\n";
echo "   - redirect(\$path) — метод для редиректа\n\n";

echo "4. Получение объектов через Config::getObject() (Singleton):\n\n";

try {
    // Получаем объект пользователя (singleton)
    $User1 = Config::getObject('core.user.class');
    echo "   ✓ Получен объект User: " . get_class($User1) . "\n";
    
    // Получаем тот же объект ещё раз
    $User2 = Config::getObject('core.user.class');
    echo "   ✓ Получен объект User второй раз\n";
    
    // Проверяем, что это один и тот же объект
    if ($User1 === $User2) {
        echo "   ✓ Это один и тот же объект (singleton)\n";
    }
    
    // Получаем объект роутера
    $router = Config::getObject('core.router.class');
    echo "   ✓ Получен объект Router: " . get_class($router) . "\n";
    
    // Получаем объект сессии
    $session = Config::getObject('core.session.class');
    echo "   ✓ Получен объект Session: " . get_class($session) . "\n\n";
    
} catch (Exception $e) {
    echo "   ✗ Ошибка: " . $e->getMessage() . "\n\n";
}

echo "5. Пример использования в контроллере:\n\n";
echo "   public function loginAction()\n";
echo "   {\n";
echo "       // Получаем объект пользователя (singleton)\n";
echo "       \$User = Config::getObject('core.user.class');\n";
echo "       \n";
echo "       if (\$User->login(\$login, \$pass)) {\n";
echo "           \$this->redirect(WebRouter::link('homepage/index'));\n";
echo "       }\n";
echo "   }\n\n";

echo "6. Жизненный цикл выполнения контроллера:\n";
echo "   1. Маршрутизация → определение контроллера и действия\n";
echo "   2. Создание объекта контроллера\n";
echo "   3. Инициализация \$this->view\n";
echo "   4. Проверка доступа (\$rules)\n";
echo "   5. Вызов действия (например, indexAction())\n";
echo "   6. Работа с данными (модели, Config::getObject())\n";
echo "   7. Передача данных в view (\$this->view->addVar())\n";
echo "   8. Рендеринг (\$this->view->render())\n\n";

echo "7. Доступные объекты через Config::getObject():\n";
echo "   - 'core.user.class' → объект пользователя (AuthUser)\n";
echo "   - 'core.router.class' → объект роутера (WebRouter)\n";
echo "   - 'core.session.class' → объект сессии (Session)\n\n";

echo "8. Преимущества использования Config::getObject():\n";
echo "   - Singleton: один объект на всё приложение\n";
echo "   - Глобальный доступ из любого места\n";
echo "   - Экономия памяти\n";
echo "   - Удобство использования\n\n";

echo "=== Демонстрация завершена ===\n";

