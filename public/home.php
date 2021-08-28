<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');

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
// 
$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];


//select テスト用
$user_id = 2;
$date_f = date('Y-m-j', $timestamp);
$date_l = date('Y-m-t', $timestamp);
//var_dump($date_f);
//var_dump($date_l);
// select
$dbh = Db::getHandle();
$r = $dbh->beginTransaction();
//users へのinsert
$sql = 'SELECT * FROM registers WHERE user_id = :user_id AND date BETWEEN :date_f AND :date_l';
$pre = $dbh->prepare($sql);
// プレースホルダに値をバインド
$pre->bindValue(':user_id', $user_id);
$pre->bindValue(':date_f', $date_f);
$pre->bindValue(':date_l', $date_l);

// sql を実行
$r = $pre->execute();
$datum = $pre->fetchALL();
// var_dump($datum);
$date_c = [];// キー=日付　中身=取引内容
$inc_all = 0;
$spe_all = 0;
foreach($datum as $d){
	$date_c[$d["date"]] = $d;
	$inc_all += $d["income"];
	$spe_all += $d["spending"];
}
var_dump($date_c);


$context = [
	'ym' => $ym,
	'prev' => $prev,
	'next' => $next,
	'today' => $today,
    'end_month' => $end_month, 
    'first_week' => $first_week,
	'aryWeek' => $aryWeek,
	'contents' => $datum,
	'incomes' => $inc_all,
	'spendings' => $spe_all
];
//出力
require_once(BASEPATH . '/libs/fin.php');
