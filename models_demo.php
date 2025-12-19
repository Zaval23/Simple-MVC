<?php
/**
 * Демонстрация работы с моделями в SimpleMVC
 * 
 * Запуск: php models_demo.php
 */

require_once 'console_autoload.php';

echo "=== Демонстрация моделей (Models) в SimpleMVC ===\n\n";

// Загружаем конфигурацию
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Роль модели в MVC:\n";
echo "   Модель отвечает за:\n";
echo "   - Работу с базой данных\n";
echo "   - Бизнес-логику\n";
echo "   - Представление сущностей (таблиц БД)\n\n";

echo "2. Структура модели в SimpleMVC:\n\n";
echo "   class MyModel extends \\ItForFree\\SimpleMVC\\MVC\\Model\n";
echo "   {\n";
echo "       public string \$tableName = 'my_table';  // Обязательно\n";
echo "       public ?int \$id = null;                 // Поля таблицы\n";
echo "       public \$name = null;\n";
echo "       public string \$orderBy = 'name ASC';    // Опционально\n";
echo "   }\n\n";

echo "3. Методы базового класса Model:\n\n";
echo "   - getById(\$id) — получение записи по ID\n";
echo "   - getList() — получение списка записей\n";
echo "   - getPage(\$page, \$size) — получение страницы (пагинация)\n";
echo "   - loadFromArray(\$array) — создание объекта из массива\n";
echo "   - delete() — удаление записи\n\n";

echo "4. Примеры использования:\n\n";

echo "   Получение записи по ID:\n";
echo "   \$model = new UserModel();\n";
echo "   \$user = \$model->getById(1);\n";
echo "   echo \$user->login;\n\n";

echo "   Получение списка:\n";
echo "   \$users = \$model->getList();\n";
echo "   foreach (\$users['results'] as \$user) {\n";
echo "       echo \$user->login;\n";
echo "   }\n\n";

echo "   Создание записи:\n";
echo "   \$newUser = \$model->loadFromArray([\n";
echo "       'login' => 'user1',\n";
echo "       'email' => 'user1@example.com'\n";
echo "   ]);\n";
echo "   \$newUser->insert();\n\n";

echo "   Обновление записи:\n";
echo "   \$user = \$model->getById(1);\n";
echo "   \$user->email = 'newemail@example.com';\n";
echo "   \$user->update();\n\n";

echo "   Удаление записи:\n";
echo "   \$user = \$model->getById(1);\n";
echo "   \$user->delete();\n\n";

echo "5. Использование в контроллере:\n\n";
echo "   public function indexAction()\n";
echo "   {\n";
echo "       \$usersModel = new \\application\\models\\UserModel();\n";
echo "       \$users = \$usersModel->getList()['results'];\n";
echo "       \$this->view->addVar('users', \$users);\n";
echo "       \$this->view->render('user/index.php');\n";
echo "   }\n\n";

echo "6. Переопределение методов:\n";
echo "   Если базовые методы не подходят, можно переопределить:\n\n";
echo "   public function insert()\n";
echo "   {\n";
echo "       // Ваша кастомная логика\n";
echo "       // Например, хеширование пароля, установка даты и т.д.\n";
echo "   }\n\n";

echo "7. Безопасность:\n";
echo "   ⚠️  Всегда используйте подготовленные запросы (Prepared Statements):\n";
echo "   \$sql = \"SELECT * FROM users WHERE login = :login\";\n";
echo "   \$st = \$this->pdo->prepare(\$sql);\n";
echo "   \$st->bindValue(\":login\", \$login, \\PDO::PARAM_STR);\n";
echo "   \$st->execute();\n\n";

echo "8. Свойства модели:\n";
echo "   - \$tableName — имя таблицы (обязательно)\n";
echo "   - \$orderBy — критерий сортировки (опционально)\n";
echo "   - Свойства-колонки — поля таблицы\n\n";

echo "=== Демонстрация завершена ===\n";
echo "\nПримечание: Для реальной работы с БД убедитесь, что:\n";
echo "1. База данных создана и настроена\n";
echo "2. Таблицы созданы\n";
echo "3. Конфигурация БД правильная в web-local.php\n";

