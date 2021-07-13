<?php // init.php
declare(strict_types=1);

ob_start();
session_start();

// 基準になるディレクトリ(最後の / はない形式で)
define('BASEPATH', realpath(__DIR__ . '/..'));
//var_dump( __DIR__ . '/..');
//var_dump( realpath(__DIR__ . '/..'));

require_once(BASEPATH . '/vendor/autoload.php');

// Twigインスタンスを生成
$path = BASEPATH . '/templates';
$twig = new \Twig\Environment( new \Twig\Loader\FilesystemLoader($path) );
    