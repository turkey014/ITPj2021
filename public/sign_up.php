<?php

$name = $_POST['name'];
$mail = $_POST['email'];
$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
$dsn = "mysql:host=localhost; dbname=AccountTakeTaka; charset=utf8";
$username = "AccountTakeTaka";
$password = "AccountTakeTaka";

try {
    $dbh = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    $msg = $e->getMessage();
}

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':email', $mail);
$stmt->execte();
$member = $stmt->fetch();
if($member['email'] === $mail) {
    $msg = '登録済みのメールアドレスです。';
    $link = '<a href="sign_up.php>戻る</a>':
} else {
    $sql = "INSERT INTO users(name, mail, pass) VALUES (:name, :mail, :pass)";
}