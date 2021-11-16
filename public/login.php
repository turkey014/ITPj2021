<?php // login.php
declare(strict_types=1);

//
require_once(__DIR__ . '/../libs/init.php');

try{
    // emailとpassの取得
    $email = strval($_POST['email'] ?? '');
    $pw = strval($_POST['pw'] ?? '');
    //$pw = password_hash($pw, PASSWORD_DEFAULT);
    
    // email と pw を検証
    if(('' === $email) || ('' === $pw)){
        throw new \Exception('brank');
    }
    
    // CSRF tokenのチェック
    $session_token = $_SESSION['users']['csrf_token'] ?? '';
    unset($_SESSION['users']['csrf_token']);
    
    $form_token = strval($_POST['csrf_token'] ?? '');
    if(false === hash_equals($session_token, $form_token)) {
        throw new \Exception('CSRF');
    }
    
    // DBから対象レコードを取得
    $sql = 'SELECT * FROM users WHERE email=:email;';
    $pre = Db::getHandle()->prepare($sql);
    $pre->bindValue('email', $email);
    $pre->execute();
    $users = $pre->fetch(PDO::FETCH_ASSOC);
    if(false === $users){
        throw new \Exception('record');
    }
    
    // パスワードを検証
    if(false === password_verify($pw, $users['password'])){
        throw new \Exception('pass');
    }
}catch(\Throwable $e){
    
    echo "<pre>";
    //var_dump($session_token);
    //var_dump($form_token);
    //var_dump($_POST['csrf_token']);
    var_dump($pw);
    var_dump($users['password']);
    var_dump($e->getMessage()); exit;
    echo "</pre>";
    
    // emailを残す(データの持ち回り)
    $_SESSION['flash']['email'] = $email;
    $_SESSION['flash']['error'] = true;
    
    // 非ログインTopPageに遷移
    header('Location: ./index.php');
    exit;
}

// 
session_regenerate_id(true); // セッション固定攻撃からの防御

// 認可をonにする
unset($users['password']); // passwordは削除
$_SESSION['users']['auth'] = $users;

// Homeへ遷移
header('Location: ./home.php');