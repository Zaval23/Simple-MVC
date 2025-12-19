<?php
/**
 * Демонстрация работы маршрутизации в SimpleMVC
 * 
 * Запуск: php routing_demo.php
 */

require_once 'console_autoload.php';

echo "=== Демонстрация маршрутизации в SimpleMVC ===\n\n";

echo "1. Формат маршрута:\n";
echo "   [namespace/]controller/action\n\n";

echo "2. Примеры маршрутов и их преобразование:\n\n";

$routes = [
    'homepage/index' => [
        'description' => 'Главная страница',
        'namespace' => 'application\\controllers',
        'controller' => 'HomepageController',
        'action' => 'indexAction()',
        'file' => 'application/controllers/HomepageController.php'
    ],
    'login/login' => [
        'description' => 'Страница входа',
        'namespace' => 'application\\controllers',
        'controller' => 'LoginController',
        'action' => 'loginAction()',
        'file' => 'application/controllers/LoginController.php'
    ],
    'admin/adminusers/index' => [
        'description' => 'Список пользователей (админка)',
        'namespace' => 'application\\controllers\\admin',
        'controller' => 'AdminusersController',
        'action' => 'indexAction()',
        'file' => 'application/controllers/admin/AdminusersController.php'
    ],
    'admin/adminusers/add' => [
        'description' => 'Добавление пользователя',
        'namespace' => 'application\\controllers\\admin',
        'controller' => 'AdminusersController',
        'action' => 'addAction()',
        'file' => 'application/controllers/admin/AdminusersController.php'
    ],
    'admin/notes/edit' => [
        'description' => 'Редактирование заметки',
        'namespace' => 'application\\controllers\\admin',
        'controller' => 'NotesController',
        'action' => 'editAction()',
        'file' => 'application/controllers/admin/NotesController.php'
    ],
];

foreach ($routes as $route => $info) {
    echo "   Маршрут: $route\n";
    echo "   Описание: {$info['description']}\n";
    echo "   → Namespace: {$info['namespace']}\n";
    echo "   → Контроллер: {$info['controller']}\n";
    echo "   → Действие: {$info['action']}\n";
    echo "   → Файл: {$info['file']}\n";
    echo "   → URL: index.php?route=$route\n\n";
}

echo "3. Алгоритм преобразования маршрута:\n";
echo "   Маршрут: admin/adminusers/index\n";
echo "   1. Разбиваем по '/': ['admin', 'adminusers', 'index']\n";
echo "   2. Определяем namespace: 'admin' → 'application\\controllers\\admin'\n";
echo "   3. Определяем контроллер: 'adminusers' → 'AdminusersController'\n";
echo "   4. Определяем действие: 'index' → 'indexAction'\n";
echo "   5. Полное имя класса: 'application\\controllers\\admin\\AdminusersController'\n";
echo "   6. Создаём объект и вызываем метод: \$controller->indexAction()\n\n";

echo "4. Использование WebRouter::link() для создания ссылок:\n\n";

// Имитируем работу WebRouter::link()
function demoLink($route) {
    return "index.php?route=$route";
}

echo "   WebRouter::link('homepage/index')\n";
echo "   → " . demoLink('homepage/index') . "\n\n";

echo "   WebRouter::link('admin/adminusers/index')\n";
echo "   → " . demoLink('admin/adminusers/index') . "\n\n";

echo "   WebRouter::link('admin/adminusers/edit&id=5')\n";
echo "   → " . demoLink('admin/adminusers/edit&id=5') . "\n\n";

echo "5. Примеры использования в коде:\n\n";

echo "   В представлении (view):\n";
echo "   <?php use ItForFree\\SimpleMVC\\Router\\WebRouter; ?>\n";
echo "   <a href=\"<?= WebRouter::link('homepage/index') ?>\">Главная</a>\n\n";

echo "   В контроллере:\n";
echo "   use ItForFree\\SimpleMVC\\Router\\WebRouter;\n";
echo "   \$this->redirect(WebRouter::link('admin/adminusers/index'));\n\n";

echo "6. Значение по умолчанию:\n";
echo "   Если маршрут не указан в запросе, используется 'homepage/index'\n";
echo "   Запрос: index.php → маршрут: homepage/index\n\n";

echo "=== Демонстрация завершена ===\n";

