<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init_auth.php');

// 
$template_filename = 'home.twig';

// 
$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']); // flashデータなので速やかに削除
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
// 
$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];


// データ入力
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	accountsCreate();
}

//当該月の regist を持ってくる
$test = Modelmine::select_mon($ym);


$all = Modelmine::select_all();
// 収入と支出の合計を配列で返す('income'=> XX,'spending'=>XX)

$sum = Modelmine::sums($test);
$sums_all = Modelmine::sums($all);
//var_dump($sums_all);
$context = [
	/* カレンダー用データ */
	'ym' => $ym,
	'prev' => $prev,
	'next' => $next,
    'end_month' => $end_month, 
    'first_week' => $first_week,
	'aryWeek' => $aryWeek,
	/* 総資産 */
	'sums_all' => $sums_all,
	/* 取得データ */ 
	'contents' => $test,
	'incomes' => $sum['inc'],
	'spendings' => $sum['spe'],
	/* 引継ぎデータ */
	'accounting' => $session['accounting'] ?? [],
];
//出力
require_once(BASEPATH . '/libs/fin.php');
