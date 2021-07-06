<?php  // login.php
declare(strict_types=1);

$mail = $_POST['mail'];
$dsn = "mysql:host=localhost; dbname=AccountTakeTaka; charset=utf8";
$username = "AccountTakeTaka";
$password = "xxxx";

try {
    $dbh = new PDO($dsn, $username, $password);
}catch(PDOException $e){
    $msg = $e->getMessage();
}

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':email', $email);
$stmt->execute();
$member = $stmt->fetch();

if(password_verify($_POST['pass'], $member['pass'])){
    $_SESSION['id'] = $member['id'];
    $_SESSION['name'] = $member['name'];
    $msg = 'ログインしました。';
    $link = '<a href="home.php">TOP</a>';
}else{
    $msg = 'メールアドレスもしくはパスワードが間違っています。';
    $link = '<a href="login.php">戻る</a>';
}