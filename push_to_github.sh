#!/bin/bash
# Скрипт для загрузки проекта в новый GitHub репозиторий

echo "=== Загрузка проекта в GitHub ==="
echo ""

NEW_REPO="https://github.com/Zaval23/Simple-MVC.git"
REPO_DIR="/var/www/my-first-cms-good/SimpleMVC-example"

cd "$REPO_DIR" || exit 1

# 1. Проверяем статус
echo "1. Проверка статуса Git..."
git status --short | head -10
echo ""

# 2. Добавляем все изменения
echo "2. Добавление всех изменений..."
git add -A
echo "   ✅ Изменения добавлены"
echo ""

# 3. Коммитим изменения (если есть)
echo "3. Создание коммита..."
if git diff --cached --quiet; then
    echo "   ℹ️  Нет изменений для коммита"
else
    git commit -m "Migrate CMS to SimpleMVC framework

- Migrated Article, Category, Subcategory models
- Created public and admin controllers
- Updated views and layouts
- Configured routing and access control
- Fixed controller naming to match SimpleMVC conventions"
    echo "   ✅ Коммит создан"
fi
echo ""

# 4. Изменяем remote origin на новый репозиторий
echo "4. Настройка нового remote..."
git remote set-url origin "$NEW_REPO"
echo "   ✅ Remote изменен на: $NEW_REPO"
echo ""

# 5. Проверяем текущую ветку
CURRENT_BRANCH=$(git branch --show-current)
echo "5. Текущая ветка: $CURRENT_BRANCH"
echo ""

# 6. Отправляем код
echo "6. Отправка кода в GitHub..."
echo "   ⚠️  Если репозиторий пустой, используйте:"
echo "      git push -u origin $CURRENT_BRANCH"
echo ""
echo "   Если репозиторий уже содержит код, может потребоваться:"
echo "      git push -u origin $CURRENT_BRANCH --force"
echo ""
read -p "Продолжить отправку? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if git push -u origin "$CURRENT_BRANCH" 2>&1; then
        echo ""
        echo "   ✅ Код успешно отправлен!"
    else
        echo ""
        echo "   ⚠️  Возможна ошибка. Проверьте:"
        echo "      - Доступ к репозиторию"
        echo "      - Наличие коммитов"
        echo "      - Может потребоваться --force (если репозиторий не пустой)"
    fi
else
    echo "   Отменено пользователем"
fi

echo ""
echo "=== Готово ==="
echo ""
echo "Проверьте репозиторий: $NEW_REPO"

