<?php // sign_up_activation.php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

// tokenを把握
$token = strval($_POST['token'] ?? '');
if('' === $token){
    // XXX
    echo 'tokenが指定されていません。';
    exit;
}

// DB上での存在確認
$dbh = Db::getHandle();

// 初めにお掃除する


$sql = 'delete from activations where activation_ttl < now();';
$pre = $dbh->prepare($sql);
//var_dump($pre);

//SQLを実行
$r = $pre->execute();
//var_dump($r);

// トランザクション開始
$r = $dbh->beginTransaction();

// tokenを検索
// XXX 上でお掃除しているのでactivation_ttlは聞かない。バッチに移動させたらactivation_ttlを追加する
$sql = 'select * from activations where activation_token = :activation_token FOR UPDATE;';
$pre = $dbh->prepare($sql);
//var_dump($pre);

// バインド
$pre->bindValue(':activation_token', $token);

// SQLを実行
$r = $pre->execute();
//var_dump($r);

$datum = $pre->fetch(\PDO::FETCH_ASSOC);
var_dump($datum);
if(false === $datum){
    // XXX 本来はエラー画面へ遷移
    echo "tokenが見つかりませんでした";
    exit;
}

// emailを認証する(=usersにemailを入れる)
$sql = 'update users set email = :email where user_id = :user_id';
$pre = $dbh->prepare($sql);
// バインド
$pre->bindValue(':email', $datum['email']);
$pre->bindValue('user_id', $datum['user_id']);
// SQLを実行
$r = $pre->execute();
var_dump($r);

// tokenを消す
$sql = 'delete from activations where activation_token = :activation_token;';
$pre = $dbh->prepare($sql);
//var_dump($pre);
// バインド
$pre->bindValue(':activation_token', $token);
// SQLを実行
$r = $pre->execute();
var_dump($r);

// トランザクション終了
$r = $dbh->commit(); // XXX

// 完了画面
echo 'fin'; // 後でlocationに書き換える