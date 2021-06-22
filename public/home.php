<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/init.php');

// 
$template_filename = 'home.twig';

/*
$context = [];

//出力
require_once(BASEPATH . '/public/fin.php');
*/

$year = date('Y');
$month = date('m');
$weeks = ['日','月','火','水','木','金','土'];
$end_month = date('t', strtotime($year.$month.'01')); // t = 月の日数
$first_month = date('w', strtotime($year.$month.'01')); // w = 初日の曜日
//$f = $first_month;
//$br = 7 - $first_month;


$context = [
	'cal' => [$year, $month, $weeks, $end_month, $first_month],
];
var_dump($context);
//出力
require_once(BASEPATH . '/public/fin.php');
