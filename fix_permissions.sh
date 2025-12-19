#!/bin/bash
# Скрипт исправления прав доступа к директории assets

echo "=== Исправление прав доступа ==="
echo ""

ASSETS_DIR="/var/www/my-first-cms-good/SimpleMVC-example/web/assets"

# 1. Создаем директорию если её нет
if [ ! -d "$ASSETS_DIR" ]; then
    echo "1. Создание директории assets..."
    mkdir -p "$ASSETS_DIR"
    if [ $? -eq 0 ]; then
        echo "   ✅ Директория создана"
    else
        echo "   ❌ Ошибка при создании директории"
        exit 1
    fi
else
    echo "1. Директория assets существует"
fi

# 2. Устанавливаем права
echo "2. Установка прав доступа..."
sudo chown -R www-data:www-data "$ASSETS_DIR"
sudo chmod -R 755 "$ASSETS_DIR"

if [ $? -eq 0 ]; then
    echo "   ✅ Права установлены"
else
    echo "   ⚠️  Возможна ошибка (но это может быть нормально)"
fi

# 3. Устанавливаем права на запись для группы
echo "3. Установка прав на запись..."
sudo chmod -R 775 "$ASSETS_DIR"

if [ $? -eq 0 ]; then
    echo "   ✅ Права на запись установлены"
fi

echo ""
echo "=== Готово! ==="
echo ""
echo "Проверьте сайт: http://mysimple.loc/"

