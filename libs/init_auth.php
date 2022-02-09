<?php
/*
 * init + 認可処理
*/
declare(strict_types=1);
//
require_once(__DIR__ . '/../libs/init.php');
require_once(__DIR__ . '/../libs/accountsCreate.php');
require_once(__DIR__ . '/../Model/Modelmine.php');

// 認可処理
if(false === isset($_SESSION['users']['auth'])){
	// 非ログインTopPageに遷移
	header('Location: ./index.php');
	exit;
}
