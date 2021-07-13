<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/init.php');

// 
$template_filename = 'home.twig';


//表示させる年月を設定　↓これは現在の月
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // 今月の年月を表示
    $ym = date('Y-m');	
}
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}
$today = date('Y-m-j');

$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

//月末日を取得
$end_month = date('t', $timestamp);

//朔日の曜日を取得
$first_week = date('w', strtotime('-1 day',$timestamp));

$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];



$context = [
	'ym' => $ym,
	'prev' => $prev,
	'next' => $next,
	'today' => $today,
    'end_month' => $end_month, 
    'first_week' => $first_week,
	'aryWeek' => $aryWeek
];
//出力
require_once(BASEPATH . '/public/fin.php');
