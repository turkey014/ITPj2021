<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/init.php');

// 
$template_filename = 'day_result.twig';


if (isset($_GET['ymd'])) {
    $ymd = strtotime($_GET['ymd']);
} else {
    // 今月の年月を表示
    echo "errer";	
}
$month = date('m', $ymd);
$day = date('d', $ymd);



$context = [
	'month' => $month,
	'day' => $day,
];

//出力
require_once(BASEPATH . '/public/fin.php');
