<?php
/**
 * Демонстрация работы с представлениями (Views) в SimpleMVC
 * 
 * Запуск: php views_demo.php
 */

require_once 'console_autoload.php';

use ItForFree\SimpleMVC\Config;

echo "=== Демонстрация представлений (Views) в SimpleMVC ===\n\n";

// Загружаем конфигурацию
$localConfig = require(__DIR__ . '/application/config/web-local.php');
$config = ItForFree\rusphp\PHP\ArrayLib\Merger::mergeRecursivelyWithReplace(
    require(__DIR__ . '/application/config/web.php'), 
    $localConfig
);
\ItForFree\SimpleMVC\Application::get()->setConfiguration($config);

echo "1. Роль представления в MVC:\n";
echo "   Представление (View) отвечает за:\n";
echo "   - Отображение данных пользователю\n";
echo "   - Форматирование данных (HTML, JSON и т.д.)\n";
echo "   - Интерфейс пользователя\n\n";

echo "2. Структура представлений в SimpleMVC:\n";
echo "   - Представления — это PHP-файлы\n";
echo "   - Находятся в директории application/views/\n";
echo "   - Содержат смесь PHP-кода и HTML\n";
echo "   - Получают данные из контроллера через переменные\n\n";

echo "3. Работа с представлениями в контроллере:\n\n";
echo "   Метод addVar() — передача переменной:\n";
echo "   \$this->view->addVar('title', 'Заголовок');\n";
echo "   → В представлении доступна переменная \$title\n\n";

echo "   Метод render() — рендеринг представления:\n";
echo "   \$this->view->render('homepage/index.php');\n";
echo "   → Подключает файл application/views/homepage/index.php\n\n";

echo "4. Пример полного цикла:\n\n";
echo "   Контроллер:\n";
echo "   public function indexAction()\n";
echo "   {\n";
echo "       \$this->view->addVar('title', 'Главная страница');\n";
echo "       \$this->view->addVar('users', ['user1', 'user2']);\n";
echo "       \$this->view->render('homepage/index.php');\n";
echo "   }\n\n";

echo "   Представление (homepage/index.php):\n";
echo "   <h1><?= \$title ?></h1>\n";
echo "   <ul>\n";
echo "       <?php foreach (\$users as \$user): ?>\n";
echo "           <li><?= \$user ?></li>\n";
echo "       <?php endforeach; ?>\n";
echo "   </ul>\n\n";

echo "5. Вывод переменных в представлениях:\n";
echo "   - Эхо-тэг: <?= \$variable ?>\n";
echo "   - Явный echo: <?php echo \$variable; ?>\n";
echo "   - Оба способа эквивалентны, но эхо-тэг короче\n\n";

echo "6. Безопасность:\n";
echo "   ⚠️  Всегда экранируйте пользовательские данные!\n";
echo "   Плохо: <?= \$userInput ?>\n";
echo "   Хорошо: <?= htmlspecialchars(\$userInput) ?>\n\n";

echo "7. Макеты (Layouts):\n";
echo "   - Макет оборачивает представление\n";
echo "   - Содержит общие части: хедер, футер, меню\n";
echo "   - Определяется в контроллере: \$layoutPath = 'main.php'\n";
echo "   - Содержимое представления доступно как \$CONTENT_DATA\n\n";

echo "8. Структура файлов представлений:\n";
echo "   application/views/\n";
echo "     ├── layouts/          # Макеты\n";
echo "     │   ├── main.php\n";
echo "     │   └── admin-main.php\n";
echo "     ├── homepage/         # Представления для HomepageController\n";
echo "     │   └── index.php\n";
echo "     ├── login/\n";
echo "     │   └── index.php\n";
echo "     └── user/\n";
echo "         ├── index.php\n";
echo "         ├── add.php\n";
echo "         └── edit.php\n\n";

echo "9. Использование Config::getObject() в представлениях:\n";
echo "   <?php\n";
echo "   use ItForFree\\SimpleMVC\\Config;\n";
echo "   \$User = Config::getObject('core.user.class');\n";
echo "   ?>\n";
echo "   <?php if (\$User->isAllowed('admin')): ?>\n";
echo "       <a href=\"/admin\">Админка</a>\n";
echo "   <?php endif; ?>\n\n";

echo "10. Разница между View, Layout и Template:\n";
echo "    - View (Представление) — конкретный файл для действия контроллера\n";
echo "    - Layout (Макет) — общая структура страницы (хедер, футер)\n";
echo "    - Template (Шаблон) — в SimpleMVC синоним макета\n\n";

echo "=== Демонстрация завершена ===\n";

