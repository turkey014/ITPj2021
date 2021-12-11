<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');
require_once(__DIR__ . '/../libs/accountsCreate.php');

// 
$template_filename = 'day_result.twig';

// 閲覧する日付の取得
if (isset($_GET['ymd'])) {
    $date = $_GET['ymd'];
} else {
    // 今月の年月を表示
    echo "errer";	
}
$month = date('m', strtotime($date));
$day = date('d', strtotime($date));

// test用
$user_id = 2;

$test = Modelmine::day_registers($user_id, $date);
$array = $test->array();
//var_dump($array);exit;
/*
foreach($array as $k => $v){
	var_dump($k, $v);
}
*/
$sum = Modelmine::sums($test);

$context = [
	'month' => $month,
	'day' => $day,
	'contents' => $array,
	'sums' => $sum,
];

//出力
require_once(BASEPATH . '/libs/fin.php');
