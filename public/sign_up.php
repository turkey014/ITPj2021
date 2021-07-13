<?php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

$name = $_POST['name'];
$mail = $_POST['email'];
$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

// フォームに入力されたメールがすでに登録されていないかチェック
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':email', $mail);
$stmt->execte();
$member = $stmt->fetch();
if($member['email'] === $mail) {
    $msg = '登録済みのメールアドレスです。';
    $link = '<a href="sign_up.php>戻る</a>':
} else {
    // 登録されていない場合はinsert
    $sql = "INSERT INTO users(name, email, pass) VALUES (:name, :email, :pass)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':pass', $pass);
    $stmt->execute();
    $msg = '会員登録が完了しました。';
    $link = '<a href="login.php">ログインページ</a>';
}