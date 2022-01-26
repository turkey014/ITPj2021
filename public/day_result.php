<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init_auth.php');

// 
$template_filename = 'day_result.twig';

// 閲覧する日付の取得
if (isset($_GET['ymd'])) {
    $date = $_GET['ymd'];
} else {
    // 今月の年月を表示
    echo "error";	
}
$month = date('m', strtotime($date));
$day = date('d', strtotime($date));

// test用
$user_id = 2;

$test = Modelmine::select_day($date);
$sum = Modelmine::sums($test);

$context = [
	'month' => $month,
	'day' => $day,
	'contents' => $test,
	'sums' => $sum,
];

//出力
require_once(BASEPATH . '/libs/fin.php');
