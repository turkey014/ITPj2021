<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');

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


//DBアクセス
//select テスト用
$user_id = 1;
// select
$dbh = Db::getHandle();
$r = $dbh->beginTransaction();
//users へのinsert
$sql = 'SELECT * FROM registers WHERE user_id = :user_id AND date = :date';
$pre = $dbh->prepare($sql);
// プレースホルダに値をバインド
$pre->bindValue(':user_id', $user_id);
$pre->bindValue(':date', $date);

// sql を実行
$r = $pre->execute();
$datum = $pre->fetchALL();
var_dump($datum);



$context = [
	'month' => $month,
	'day' => $day,
];

//出力
require_once(BASEPATH . '/libs/fin.php');
