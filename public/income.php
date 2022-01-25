<?php
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');
require_once(__DIR__ . '/../Model/Modelmine.php');

// 
$template_filename = 'income.twig';

/* 設定 */
$limit_num = 10; //1ページ当たりの表示数


// 情報があったら受け取る
$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']); // flashデータなので速やかに削除


// ページ数の取得とざっくりしたfilter
$p = intval($_GET['p'] ?? 1);
if(1 > $p){
	$p = 1;
}

$sort = strval($_GET['sort'] ?? '');
//収入データの取得
//当該月の regist を持ってくる
$amount_flg = 'income';
$key = 2; // user_id;
$data = Modelmine::select_f($limit_num, $p, $sort, $amount_flg);
$list = $data['list'];
$search_string_e = $data['search_string_e'];
$from_date = $data['from_date'];
$to_date = $data['to_date'];
$subject_search = $data['subject_search'];


// 「前」「次」の有無の確認
$before_page = $p - 1; // 前がないなら0になるから後はテンプレート側で判定

if(count($list) >= $limit_num + 1){ //sqlで一つ多く取って、それより少なければ最終ページになる
	$next_page = $p + 1;  	
}else{
	$next_page = 0;
	
}
/**/
$context = [
	'data' => $list,
	// ページング用の情報
	'now_page' => $p,
	'next_page' => $next_page,
	'before_page'  => $before_page,
	'search_string_e' => $search_string_e,
	//検索用情報
	'from_date' => $from_date,
	'to_date' => $to_date,
	'subject_search' => $subject_search,
	// sort用情報
	'sort' => $sort,
	// CSRF用
	//'csrf_token' => $csrf_token,
	
];

//出力

require_once(BASEPATH . '/libs/fin.php');