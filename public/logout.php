<?php // logout.php
declare(strict_types=1);
require_once(__DIR__ . '/../libs/init.php');

// ログアウト処理
unset($_SESSION['users']);

//
header('Location: ./index.php');
exit;