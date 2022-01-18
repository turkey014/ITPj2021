<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');
require_once(__DIR__ . '/../libs/accountsCreate.php');

// 
$template_filename = 'home.twig';


// 
$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']); // flashデータなので速やかに削除
//var_dump($session);
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

//select テスト用
$user_id = 2;
$date_f = date('Y-m-j', $timestamp);
$date_l = date('Y-m-t', $timestamp);


// データ入力
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	accountsCreate();
}

//当該月の regist を持ってくる
$test = Modelmine::select_month($user_id, $ym);

$array = $test->array();

// 収入と支出の合計を配列で返す('income'=> XX,'spending'=>XX)
$sum = ModelMine::sums($test);
$context = [
	'ym' => $ym,
	'prev' => $prev,
	'next' => $next,
	//'today' => $today,
    'end_month' => $end_month, 
    'first_week' => $first_week,
	'aryWeek' => $aryWeek,
	
	'contents' => $array,
	'incomes' => $sum['inc'],
	'spendings' => $sum['spe'],
	'accounting' => $session['accounting'] ?? [],
];
//出力
require_once(BASEPATH . '/libs/fin.php');
