#!/bin/bash
# Скрипт для настройки прав доступа к файлам проекта SimpleMVC
# Использование: sudo ./setup_permissions.sh

PROJECT_DIR="/var/www/my-first-cms-good/SimpleMVC-example"
USER_NAME="user"
GROUP_NAME="www-data"

echo "Настройка прав доступа для проекта SimpleMVC..."
echo "Проект: $PROJECT_DIR"
echo "Пользователь: $USER_NAME"
echo "Группа: $GROUP_NAME"
echo ""

# Проверка, что скрипт запущен с правами root
if [ "$EUID" -ne 0 ]; then 
    echo "ОШИБКА: Этот скрипт должен быть запущен с правами sudo"
    echo "Использование: sudo ./setup_permissions.sh"
    exit 1
fi

# Устанавливаем владельца и группу для всего проекта
echo "1. Установка владельца и группы для файлов проекта..."
chown -R $USER_NAME:$GROUP_NAME "$PROJECT_DIR"

# Настраиваем права для директорий
echo "2. Настройка прав для директорий..."
find "$PROJECT_DIR" -type d -exec chmod 2775 {} \;
# 2775 = rwxrwsr-x (чтение, запись, выполнение для владельца и группы, чтение и выполнение для остальных)
# s в группе означает setgid - новые файлы будут создаваться с той же группой

# Настраиваем права для файлов
echo "3. Настройка прав для файлов..."
find "$PROJECT_DIR" -type f -exec chmod 664 {} \;
# 664 = rw-rw-r-- (чтение и запись для владельца и группы, только чтение для остальных)

# Особое внимание к директории web/assets/ - здесь нужны права на запись
echo "4. Настройка специальных прав для web/assets/..."
if [ -d "$PROJECT_DIR/web/assets" ]; then
    chmod 2775 "$PROJECT_DIR/web/assets"
    chown $USER_NAME:$GROUP_NAME "$PROJECT_DIR/web/assets"
    echo "   ✓ Права на web/assets/ настроены"
fi

# Настраиваем права для исполняемых файлов (если есть)
echo "5. Настройка прав для исполняемых файлов..."
find "$PROJECT_DIR" -name "*.sh" -o -name "*.php" | while read file; do
    chmod 775 "$file"
done

# Настраиваем права для composer.phar (если есть)
if [ -f "$PROJECT_DIR/composer.phar" ]; then
    chmod 775 "$PROJECT_DIR/composer.phar"
    echo "   ✓ Права на composer.phar настроены"
fi

echo ""
echo "✓ Настройка прав завершена!"
echo ""
echo "ВАЖНО: Убедитесь, что ваш пользователь добавлен в группу www-data:"
echo "  groups  # должен показывать www-data в списке"
echo ""
echo "Если www-data нет в списке групп, выполните:"
echo "  sudo usermod -a -G www-data $USER_NAME"
echo "  su - $USER_NAME  # или перезагрузите систему"
echo ""

