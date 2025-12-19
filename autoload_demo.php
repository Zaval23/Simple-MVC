<?php
/**
 * Демонстрация работы автозагрузки классов в SimpleMVC
 * 
 * Этот файл показывает, как работает автозагрузка на практике.
 * Запустите его через PHP CLI: php autoload_demo.php
 */

echo "=== Демонстрация автозагрузки классов ===\n\n";

// 1. Подключаем автозагрузку (используем console_autoload.php для запуска из корня)
echo "1. Подключение автозагрузки...\n";
require_once 'console_autoload.php';
echo "   ✓ Автозагрузка подключена\n\n";

// 2. Теперь можем использовать классы БЕЗ require
echo "2. Использование класса контроллера:\n";
// Проверяем, что класс можно загрузить (автозагрузка сработает)
if (class_exists('\application\controllers\HomepageController')) {
    echo "   ✓ Класс HomepageController найден и загружен автоматически\n";
    echo "   ✓ Файл: application/controllers/HomepageController.php\n";
    echo "   ✓ Без автозагрузки пришлось бы писать: require_once('application/controllers/HomepageController.php')\n\n";
} else {
    echo "   ✗ Класс не найден\n\n";
}

// 3. Использование класса модели
echo "3. Использование класса модели:\n";
try {
    $note = new \application\models\Note();
    echo "   ✓ Класс Note загружен автоматически\n";
    echo "   ✓ Файл найден: application/models/Note.php\n\n";
} catch (Exception $e) {
    echo "   ✗ Ошибка: " . $e->getMessage() . "\n\n";
}

// 4. Использование класса из ядра (через Composer)
echo "4. Использование класса из ядра (через Composer):\n";
try {
    // Проверяем существование класса (автозагрузка сработает автоматически)
    if (class_exists('ItForFree\SimpleMVC\Config')) {
        echo "   ✓ Класс Config доступен через автозагрузку Composer\n";
        echo "   ✓ Файл найден в vendor/it-for-free/simple-mvc/\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Ошибка: " . $e->getMessage() . "\n\n";
}

// 5. Показываем, как namespace преобразуется в путь
echo "5. Преобразование namespace в путь файла:\n";
echo "   application\\controllers\\HomepageController\n";
echo "   → application/controllers/HomepageController.php\n\n";

echo "   application\\models\\Note\n";
echo "   → application/models/Note.php\n\n";

echo "   ItForFree\\SimpleMVC\\MVC\\Controller\n";
echo "   → vendor/it-for-free/simple-mvc/src/MVC/Controller.php (PSR-4)\n\n";

echo "=== Демонстрация завершена ===\n";

