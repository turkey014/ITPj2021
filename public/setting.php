<?php // setting.php
declare(strict_types=1);

//
require_once(__DIR__ . '/../libs/init.php');

$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

$user_id = $_SESSION['users']['auth']['user_id'];


// 登録されているユーザー情報(メール、名前、登録日)の取得 SELECT user_name, email, created_at FROM users WHERE user_id = :user_id
$sql = 'SELECT * FROM users WHERE user_id = :user_id;';
$pre = Db::getHandle()->prepare($sql);
$pre->bindValue(':user_id',$user_id);
$r = $pre->execute();
$users = $pre->fetch(PDO::FETCH_ASSOC);
var_dump($users);

// メールアドレスの変更 UPDATE users SET email = :new_email WHERE user_id = :user_id
try{
    $new_email = strval($_POST['new_email'] ?? '');
    $password = strval($_POST['password'] ?? '');

    // email と password　を検証
    if (('' === $new_email) || ('' === $password)) {
        throw new \Exception('brank');
    }
    
    // password があっているか
    if (false === password_verify($password, $users['password'])) {
        throw new \Exception('password error');
    }
    
    // new_email のフォーマットチェック
    if(false === filter_var($new_email, FILTER_VALIDATE_EMAIL)){
        throw new \Exception('email format error');
    }
    
    // XXXXXXXXXX
    $sql = 'UPDATE users SET email = :new_email WHERE user_id = :user_id;';
    $pre = Db::getHandle()->prepare($sql);
    $pre->bindValue(':user_id',$user_id);
    $pre->bindValue(':new_email',$new_email);
    $pre->execute();
    $users = $pre->fetch(PDO::FETCH_ASSOC);
    var_dump($users);

}catch(\Throwable $e){
    var_dump($e->getMessage()); exit;

    #header('Location: ./setting.php');
    #exit;
}

// パスワードの変更 UPDATE users SET password = new_password WHERE user_id = :user_id
// 入力されたパスワードが正しいか確認する
// 確認用パスワードと新パスワードが一致するか確認

// データリセット DELETE
// 入力されたパスワードが正しいか確認する

// アカウント削除 DELETE
// 入力されたパスワードが正しいか確認する


$template_filename = 'setting.twig';
$context = [
    'user_name' => $users['user_name'],
    'email' => $users['email'],
    'created_at' => $users['created_at'],
];

// 出力
require_once(BASEPATH . '/libs/fin.php');
