<?php 
use ItForFree\SimpleAsset\SimpleAssetManager;
use application\assets\CustomCSSAsset;


CustomCSSAsset::add();
SimpleAssetManager::printCss();
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?= $pageTitle ?? 'Widget News' ?></title>
</head>
