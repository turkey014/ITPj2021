<?php
require_once(BASEPATH . '/Model/Modelmine.php');
/*
 * 入出金処理の共通化
 * @param string $type 'income' または　'spending' 
 */

function accountsCreate(){
	//セッションから代入
	$user_id = $_SESSION['users']['auth']['user_id'];
	$type = strval($_POST['type'] ?? '');
	$date = strval($_POST['date'] ?? '');
	$subject = strval($_POST['subject'] ?? '');
	$amount = strval($_POST['amount'] ?? 0);

	//タグの取得
	$tags = [];
	foreach($_POST['tags'] as $v){
		if('' !== $v){
			$tags[] = strval($v);
		}
	}

	// validate
	$error = [];
	// type
	if(($type != 'income') && ($type != 'spending') ){
		$error['type'] = true;
	}
	// 日付
	$t = strtotime($date);
	if(false === $t){
		$error['date'] = true;
	} else {
		$date = date('Y-m-d', $t);
	}
	// 科目
	if(0 === strlen($subject)){
		$error['subject'] = true;
	}
	// 金額
	if(0 >= $amount){
		$error['amount'] = true; 
	}
	//var_dump($error);exit;
	//
	if([] !== $error){
		// accounting配下にデータをまとめとく
		$_SESSION['flash']['accounting']['error'] = true;
		$_SESSION['flash']['accounting']['type'] = $type;
		$_SESSION['flash']['accounting']['date'] = $date;
		$_SESSION['flash']['accounting']['subject'] = $subject;
		$_SESSION['flash']['accounting']['amount'] = $amount;
		$_SESSION['flash']['accounting']['tags'] = $tags;
		//
		var_dump($_SESSION['flash']['accounting']);
		header('Location: ./home.php');
		exit;
	}
	
	// ここまできたら validate OK
	// データのINSERT

	$data = [
		'user_id' => $user_id,
		'date' => $date,
		'subject' => $subject,
		$type => $amount,
		'created_at' => date('Y-m-d H:i:s'),
	];
	$r = Modelmine::create($data);
	if('' !== $tags){
		// タグの追加
		Modelmine::tag_create($user_id, $tags);
	};
	// top pageに遷移
	$_SESSION['flash']['accounting']['success'] = true;
	header('Location: ./home.php'); // 二重投稿を避けるためにリダイレクトをかけたほうがいい
	exit;
}