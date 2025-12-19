#!/bin/bash
# Скрипт настройки доступа к БД

echo "=== Настройка доступа к базе данных ==="
echo ""

DB_NAME="smvcbase"
DB_USER="smvc"
DB_PASS="1234"

# 1. Создание пользователя БД
echo "1. Создание пользователя БД '$DB_USER'..."
sudo mysql << EOF
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SELECT 'User created successfully' AS Status;
EOF

if [ $? -eq 0 ]; then
    echo "   ✅ Пользователь создан"
else
    echo "   ❌ Ошибка при создании пользователя"
    exit 1
fi

# 2. Проверка подключения
echo "2. Проверка подключения к БД..."
mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME; SELECT 'Connection OK' AS Status;" 2>&1

if [ $? -eq 0 ]; then
    echo "   ✅ Подключение успешно"
else
    echo "   ⚠️  Проблема с подключением (возможно БД не существует)"
    echo "   Создайте БД: CREATE DATABASE IF NOT EXISTS $DB_NAME;"
fi

# 3. Обновление конфигурации
echo "3. Обновление конфигурации web-local.php..."
CONFIG_FILE="/var/www/my-first-cms-good/SimpleMVC-example/application/config/web-local.php"

if [ -f "$CONFIG_FILE" ]; then
    # Создаем резервную копию
    cp "$CONFIG_FILE" "${CONFIG_FILE}.backup"
    
    # Заменяем username на smvc
    sed -i "s/'username' => 'root'/'username' => '$DB_USER'/" "$CONFIG_FILE"
    
    echo "   ✅ Конфигурация обновлена"
    echo "   Резервная копия: ${CONFIG_FILE}.backup"
else
    echo "   ❌ Файл конфигурации не найден"
fi

echo ""
echo "=== Готово! ==="
echo ""
echo "Проверьте сайт: http://mysimple.loc/"

