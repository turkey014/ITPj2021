<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/init.php');

// 
$template_filename = 'day_result.twig';

// 閲覧する日付の取得
if (isset($_GET['ymd'])) {
    $date = strtotime($_GET['ymd']);
} else {
    // 今月の年月を表示
    echo "errer";	
}
$month = date('m', $date);
$day = date('d', $date);


//DBアクセス

/*
//testデータ
$user_id = 1;



$r = $dbh->beginTransaction();
//register から回収
$sql = 'SELECT * FROM register WHERE user_id=:user_id AND date=:date;';
$pre = $dbh->prepare($sql);
var_dump($pre);

// プレースホルダに値をバインド
$pre->bindValue(':user_id', $user_id);
$pre->bindValue(':date', $date);

// sql を実行
//$r = $pre->execute();
//var_dump($r);
*/


$context = [
	'month' => $month,
	'day' => $day,
];

//出力
require_once(BASEPATH . '/public/fin.php');
