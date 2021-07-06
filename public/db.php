<?php // db.php
declare(strict_types=1);

$dsn = 'mysql:host=localhost;dbname=AccountTakeTaka;charset=UTF8mb4';
$username = 'AccountTakeTaka';
$password = 'xxxxx';
$options = [
    \PDO::ATTR_EMULATE_PREPARES => false, // エミュレート無効
    \PDO::MYSQL_ATTR_MULTI_STATEMENTS => false, // 複文無効
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる(好み)
];

try {
    $dbh = new \PDO($dsn, $username, $password, $options);
}catch( \PDOException $e){
    echo $e->getMessage(); // XXX 実際は出力しない(logに書くとか)
    exit;
}

$dbh = \PDO($dsn, $username, $password, $options);
var_dump($dbh);