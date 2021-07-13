<?php
declare(strict_types=1);

if(false === defined('BASEPATH')){
    define('BASEPATH', realpath(__DIR__ . '/..'));
}

require_once(BASEPATH . '/libs/Config.php');

class Db {
    public static function getHandle() {
        
        static $dbh = null;
        
        if(null === $dbh){
            $db_conf = Config::get('db');
            $dsn = "mysql:host={$db_conf['host']};dbname={$db_conf['dbname']};charset={$db_conf['charset']}";
            $options = [
                \PDO::ATTR_EMULATE_PREPARES => false, // エミュレート無効
                \PDO::MYSQL_ATTR_MULTI_STATEMENTS => false, // 複文無効
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる(好み)
            ];
            
            try {
                $dbh = new \PDO($dsn, $db_conf['user'], $db_conf['pass'], $options);
            }catch( \PDOException $e){
                echo $e->getMessage(); // XXX 実際は出力しない(logに書くとか)
                exit;
            }
        }
        
        return $dbh;
    }
}