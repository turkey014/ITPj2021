<?php // register.php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

//var_dump($_SESSION);
$data = $_SESSION['flash']['data'] ?? [];
$error_messages = $_SESSION['flash']['error_messages'] ?? [];
//var_dump($data, $error_messages);

// セッションの情報は削除する
unset( $_SESSION['flash']);

$template_filename = 'sign_up.twig';
$context = [
    'data' => $data,
    'error_messages' => $error_messages,
];

// 出力
require_once(BASEPATH . '/libs/fin.php');