<?php // sign_up_fin_all.php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');

//
// ログアウト処理
unset($_SESSION['users']);


$template_filename = 'sign_up_fin_all.twig';
$context = [];

// 出力
require_once(BASEPATH . '/libs/fin.php');