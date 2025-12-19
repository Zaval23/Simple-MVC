#!/bin/bash
# Скрипт создания базы данных и импорта дампов

echo "=== Создание базы данных и импорт дампов ==="
echo ""

DB_NAME="smvcbase"
DB_USER="smvc"
DB_PASS="1234"
DUMP_DIR="/var/www/my-first-cms-good/SimpleMVC-example/dump"

# 1. Создание базы данных
echo "1. Создание базы данных '$DB_NAME'..."
mysql -u "$DB_USER" -p"$DB_PASS" << EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE $DB_NAME;
SELECT 'Database created successfully' AS Status;
EOF

if [ $? -eq 0 ]; then
    echo "   ✅ База данных создана"
else
    echo "   ❌ Ошибка при создании базы данных"
    exit 1
fi

# 2. Импорт базового дампа
echo "2. Импорт базового дампа..."
if [ -f "$DUMP_DIR/basedump.sql" ]; then
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$DUMP_DIR/basedump.sql" 2>&1 | grep -v "Using a password"
    if [ $? -eq 0 ]; then
        echo "   ✅ Базовый дамп импортирован"
    else
        echo "   ⚠️  Возможна ошибка при импорте базового дампа"
    fi
else
    echo "   ⚠️  Файл basedump.sql не найден"
fi

# 3. Импорт миграции
echo "3. Импорт миграции..."
if [ -f "$DUMP_DIR/migration_cms.sql" ]; then
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$DUMP_DIR/migration_cms.sql" 2>&1 | grep -v "Using a password"
    if [ $? -eq 0 ]; then
        echo "   ✅ Миграция импортирована"
    else
        echo "   ⚠️  Возможна ошибка при импорте миграции (может быть не критично)"
    fi
else
    echo "   ⚠️  Файл migration_cms.sql не найден"
fi

# 4. Проверка таблиц
echo "4. Проверка таблиц..."
TABLES=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES;" 2>/dev/null | wc -l)
if [ "$TABLES" -gt 1 ]; then
    echo "   ✅ Найдено таблиц: $((TABLES - 1))"
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES;" 2>/dev/null | tail -n +2
else
    echo "   ⚠️  Таблицы не найдены"
fi

echo ""
echo "=== Готово! ==="
echo ""
echo "База данных '$DB_NAME' создана и настроена!"
echo "Проверьте сайт: http://mysimple.loc/"

