<?php
/**
 * Демонстрация контроля доступа в SimpleMVC
 * 
 * Запуск: php access_control_demo.php
 */

require_once 'console_autoload.php';

use ItForFree\SimpleMVC\Config;

echo "=== Демонстрация контроля доступа в SimpleMVC ===\n\n";

// Загружаем конфигурацию
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Что такое контроль доступа:\n";
echo "   Контроль доступа (Authorization) определяет:\n";
echo "   - Какие действия может выполнять пользователь\n";
echo "   - В зависимости от его роли и прав\n\n";

echo "2. Разница:\n";
echo "   - Аутентификация — проверка подлинности ('Кто вы?')\n";
echo "   - Авторизация — проверка прав ('Что вы можете делать?')\n\n";

echo "3. Роли пользователей:\n";
echo "   - 'guest' или '?' — неавторизованный пользователь\n";
echo "   - '@' — любой авторизованный пользователь\n";
echo "   - 'admin' — администратор\n";
echo "   - 'auth_user' — обычный авторизованный пользователь\n\n";

echo "4. Правила доступа в контроллерах:\n\n";
echo "   protected array \$rules = [\n";
echo "       ['allow' => true, 'roles' => ['admin']],    // Разрешить админам\n";
echo "       ['allow' => false, 'roles' => ['?', '@']],   // Запретить остальным\n";
echo "   ];\n\n";

echo "5. Примеры правил:\n\n";
echo "   Только для админов:\n";
echo "   ['allow' => true, 'roles' => ['admin']],\n";
echo "   ['allow' => false, 'roles' => ['?', '@']],\n\n";

echo "   Только для авторизованных:\n";
echo "   ['allow' => true, 'roles' => ['@']],\n";
echo "   ['allow' => false, 'roles' => ['?']],\n\n";

echo "   Для всех:\n";
echo "   ['allow' => true, 'roles' => ['?', '@']],\n\n";

echo "   Разные правила для разных действий:\n";
echo "   ['allow' => true, 'roles' => ['?'], 'actions' => ['login']],\n";
echo "   ['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],\n\n";

echo "6. Проверка доступа в коде:\n\n";
echo "   Метод isAllowed():\n";
echo "   \$User = Config::getObject('core.user.class');\n";
echo "   if (\$User->isAllowed('admin/users/index')) {\n";
echo "       // Доступ разрешён\n";
echo "   }\n\n";

echo "   Метод returnIfAllowed():\n";
echo "   echo \$User->returnIfAllowed('admin/edit', '<a href=\"/edit\">Редактировать</a>');\n";
echo "   // Вернёт HTML если доступ есть, иначе пустую строку\n\n";

echo "7. Использование в представлениях:\n\n";
echo "   Условный вывод:\n";
echo "   <?php if (\$User->isAllowed('admin/users')): ?>\n";
echo "       <a href=\"/admin/users\">Управление пользователями</a>\n";
echo "   <?php endif; ?>\n\n";

echo "   Краткий вариант:\n";
echo "   <?= \$User->returnIfAllowed('admin/edit', '<a href=\"/edit\">Редактировать</a>') ?>\n\n";

echo "8. Как применяются правила:\n";
echo "   1. Правила проверяются сверху вниз\n";
echo "   2. Первое подходящее правило определяет результат\n";
echo "   3. Если правило подходит по роли, применяется его 'allow'\n";
echo "   4. Если ни одно правило не подошло, доступ запрещён\n\n";

echo "9. Обработка ошибок доступа:\n";
echo "   Если доступ запрещён, выбрасывается исключение:\n";
echo "   SmvcAccessException\n\n";

echo "10. Лучшие практики:\n";
echo "    - Используйте принцип наименьших привилегий\n";
echo "    - Явно указывайте правила (что разрешено, что запрещено)\n";
echo "    - Группируйте правила по логике\n";
echo "    - Проверяйте доступ в представлениях перед показом элементов\n\n";

echo "=== Демонстрация завершена ===\n";

