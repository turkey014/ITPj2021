<?php
declare(strict_types=1);

//
require_once(__DIR__ . '/../libs/init.php');
require_once(BASEPATH . '/libs/Model.php');

class TestModel extends ModelBase{
	protected static $table_name = 'registers';
	protected static $primary_key = 'user_id';
}
// selectのテスト
$obj = TestModel::find(2,'2021-08-01','2021-08-31');
var_dump($obj->test);
