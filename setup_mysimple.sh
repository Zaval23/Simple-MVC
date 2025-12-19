#!/bin/bash
# Скрипт настройки домена mysimple.loc

echo "=== Настройка mysimple.loc ==="
echo ""

# 1. Создание конфигурации Apache
echo "1. Создание конфигурации Apache..."
sudo tee /etc/apache2/sites-available/mysimple.loc.conf > /dev/null << 'APACHE_CONFIG'
<VirtualHost *:80>
    ServerName mysimple.loc
 
    DocumentRoot /var/www/my-first-cms-good/SimpleMVC-example/web
    <Directory /var/www/my-first-cms-good/SimpleMVC-example/web>
        AllowOverride All
        Require all granted
    </Directory>
 
    CustomLog /var/log/apache2/mysimple.access.log common  
    ErrorLog  /var/log/apache2/mysimple.error.log
    LogLevel warn
</VirtualHost>
APACHE_CONFIG

if [ $? -eq 0 ]; then
    echo "   ✅ Конфигурация создана"
else
    echo "   ❌ Ошибка при создании конфигурации"
    exit 1
fi

# 2. Включение виртуального хоста
echo "2. Включение виртуального хоста..."
sudo a2ensite mysimple.loc.conf > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "   ✅ Виртуальный хост включен"
else
    echo "   ⚠️  Возможно уже включен"
fi

# 3. Проверка конфигурации
echo "3. Проверка конфигурации Apache..."
sudo apache2ctl configtest

if [ $? -eq 0 ]; then
    echo "   ✅ Конфигурация корректна"
else
    echo "   ❌ Ошибка в конфигурации"
    exit 1
fi

# 4. Перезагрузка Apache
echo "4. Перезагрузка Apache..."
sudo systemctl reload apache2

if [ $? -eq 0 ]; then
    echo "   ✅ Apache перезагружен"
else
    echo "   ❌ Ошибка при перезагрузке Apache"
    exit 1
fi

# 5. Добавление в hosts
echo "5. Добавление домена в /etc/hosts..."
if grep -q "mysimple.loc" /etc/hosts; then
    echo "   ⚠️  Домен уже есть в hosts"
else
    echo "127.0.0.1    mysimple.loc" | sudo tee -a /etc/hosts > /dev/null
    if [ $? -eq 0 ]; then
        echo "   ✅ Домен добавлен в hosts"
    else
        echo "   ❌ Ошибка при добавлении в hosts"
        exit 1
    fi
fi

echo ""
echo "=== Готово! ==="
echo ""
echo "Сайт доступен по адресу: http://mysimple.loc/"
echo ""
echo "Если появится ошибка 500, настройте БД (см. ERROR_500_FIX.md)"

