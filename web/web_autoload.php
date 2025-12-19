<?php
use ItForFree\rusphp\File\Path;

require_once __DIR__ . '/../base_autoload.php';

function autoload($className)
{
    // Для встроенного PHP сервера DOCUMENT_ROOT может быть не установлен
    if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
        $baseDir = Path::addToDocumentRoot('..' . DIRECTORY_SEPARATOR);
    } else {
        // Если DOCUMENT_ROOT не установлен, используем абсолютный путь
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
    baseAutoload($className, $baseDir);
}

// регистрируем функцию автозагрузки
spl_autoload_register('autoload'); 

require_once __DIR__ . '/../vendor/autoload.php';