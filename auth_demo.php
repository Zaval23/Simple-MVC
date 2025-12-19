<?php
/**
 * Демонстрация работы авторизации в SimpleMVC
 * 
 * Запуск: php auth_demo.php
 */

require_once 'console_autoload.php';

use ItForFree\SimpleMVC\Config;

echo "=== Демонстрация авторизации в SimpleMVC ===\n\n";

// Загружаем конфигурацию
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Основные понятия:\n";
echo "   - Аутентификация (Authentication) — проверка подлинности (логин/пароль)\n";
echo "   - Авторизация (Authorization) — проверка прав доступа\n\n";

echo "2. Компоненты системы авторизации:\n";
echo "   - Класс AuthUser — управление авторизацией\n";
echo "   - Сессии — хранение информации о пользователе\n";
echo "   - Роли — определение прав доступа\n";
echo "   - Контроль доступа — правила в контроллерах\n\n";

echo "3. Получение объекта пользователя:\n";
echo "   use ItForFree\\SimpleMVC\\Config;\n";
echo "   \$User = Config::getObject('core.user.class');\n\n";

try {
    $User = Config::getObject('core.user.class');
    echo "   ✓ Объект User получен: " . get_class($User) . "\n\n";
} catch (Exception $e) {
    echo "   ✗ Ошибка: " . $e->getMessage() . "\n\n";
}

echo "4. Процесс входа в систему:\n";
echo "   Пользователь → Форма входа → LoginController → AuthUser::login() → Проверка БД → Сессия\n\n";

echo "   Пример в контроллере:\n";
echo "   \$User = Config::getObject('core.user.class');\n";
echo "   if(\$User->login(\$login, \$pass)) {\n";
echo "       // Успешная авторизация\n";
echo "       \$this->redirect(WebRouter::link('homepage/index'));\n";
echo "   } else {\n";
echo "       // Ошибка авторизации\n";
echo "   }\n\n";

echo "5. Процесс выхода из системы:\n";
echo "   \$User = Config::getObject('core.user.class');\n";
echo "   \$User->logout();\n\n";

echo "6. Хранение паролей (безопасность):\n";
echo "   ⚠️  Пароли НИКОГДА не хранятся в открытом виде!\n";
echo "   - Используется соль (salt) — случайное число\n";
echo "   - Используется хеширование — password_hash() с bcrypt\n";
echo "   - Проверка — password_verify()\n\n";

echo "7. Работа с текущим пользователем:\n";
echo "   \$User = Config::getObject('core.user.class');\n";
echo "   \$login = \$User->userName;  // Логин\n";
echo "   \$role = \$User->role;       // Роль\n\n";

echo "8. Проверка доступа:\n";
echo "   if (\$User->isAllowed('admin/users/index')) {\n";
echo "       // Доступ разрешён\n";
echo "   }\n\n";

echo "9. Контроль доступа в контроллерах:\n";
echo "   protected array \$rules = [\n";
echo "       ['allow' => true, 'roles' => ['admin']],     // Разрешить админам\n";
echo "       ['allow' => false, 'roles' => ['?', '@']],   // Запретить остальным\n";
echo "   ];\n\n";

echo "10. Роли пользователей:\n";
echo "    - 'guest' — неавторизованный пользователь\n";
echo "    - '@' — любой авторизованный пользователь\n";
echo "    - 'admin' — администратор\n";
echo "    - 'auth_user' — обычный пользователь\n\n";

echo "11. Примеры правил доступа:\n";
echo "    Только для админов:\n";
echo "    ['allow' => true, 'roles' => ['admin']],\n";
echo "    ['allow' => false, 'roles' => ['?', '@']],\n\n";

echo "    Только для авторизованных:\n";
echo "    ['allow' => true, 'roles' => ['@']],\n";
echo "    ['allow' => false, 'roles' => ['?']],\n\n";

echo "    Для всех:\n";
echo "    ['allow' => true, 'roles' => ['?', '@']],\n\n";

echo "=== Демонстрация завершена ===\n";
echo "\nПримечание: Для реальной работы с авторизацией убедитесь, что:\n";
echo "1. База данных создана и настроена\n";
echo "2. Таблица users создана\n";
echo "3. Конфигурация БД правильная в web-local.php\n";
echo "4. Класс AuthUser правильно настроен в конфигурации\n";

