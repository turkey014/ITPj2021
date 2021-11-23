<?php // sign_up_fin_print.php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

// XXX 本来は絶対にやらない 学校のサーバなのでやむなし
$activation_token = $_SESSION['activation_token'] ?? '';
unset($_SESSION['activation_token']);
var_dump($activation_token);


$template_filename = 'sign_up_fin_print.twig';
$context = [];

// 出力
require_once(BASEPATH . '/libs/fin.php');