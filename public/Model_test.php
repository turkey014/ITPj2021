<?php
declare(strict_types=1);

//
require_once(__DIR__ . '/../libs/init.php');
require_once(BASEPATH . '/libs/Model.php');

class TestModel extends ModelBase{
	protected static $table_name = 'registers';
	protected static $primary_key = 'user_id';
}

$id = 2;
$day_f = '2021-08-01';
$day_l = '2021-08-31';
// selectのテスト
$obj = TestModel::find($id,$day_f,$day_l);
var_dump($obj);