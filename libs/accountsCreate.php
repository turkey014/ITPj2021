<?php
require_once(BASEPATH . '/Model/Modelmine.php');
/*
 * 入出金処理の共通化
 * @param string $type 'income' または　'spending' 
 */

function accountsCreate(){
	var_dump($_POST);
	$type = strval($_POST['type'] ?? '');
	$date = strval($_POST['date'] ?? '');
	$subject = strval($_POST['subject'] ?? '');
	$amount = strval($_POST['amount'] ?? 0);
	// var_dump($date, $subject, $amount);

	// validate
	$error = [];
	// type
	if($type != 'income' || $type != 'spending'){
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

	//
	if([] !== $error){
		// accounting配下にデータをまとめとく
		$_SESSION['flash']['accounting']['error'] = true;
		$_SESSION['flash']['accounting']['type'] = $type;
		$_SESSION['flash']['accounting']['date'] = $date;
		$_SESSION['flash']['accounting']['subject'] = $subject;
		$_SESSION['flash']['accounting']['amount'] = $amount;
		//
		header('Location: ./home.php');
		exit;
	}
	
	
	// ここまできたら validate OK
	// データのINSERT

	$data = [
		//'user_id' => $_SESSION['users']['auth']['user_id'],
		'user_id' => 2,
		'date' => $date,
		'subject' => $subject,
		'amount' => $amount,
		'created_at' => date('Y-m-d H:i:s'),
	];
	$r = Modelmine::create($data, $type);
	
	// top pageに遷移
	$_SESSION['flash']['accounting']['success'] = true;
	header('Location: ./top.php'); // 二重投稿を避けるためにリダイレクトをかけたほうがいい
	exit;
}