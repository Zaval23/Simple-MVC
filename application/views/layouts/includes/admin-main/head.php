<?php 
use ItForFree\SimpleAsset\SimpleAssetManager;
use application\assets\BootstrapAsset;

// Подключаем только Bootstrap для админки (без стилей из style.css)
BootstrapAsset::add();
SimpleAssetManager::printCss();
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?= $pageTitle ?? 'Админка | Widget News' ?></title>
</head>

