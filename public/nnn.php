<?php
declare(strict_types=1);
require_once(__DIR__ . '/../libs/ModelBase.php');

class TestModel extends ModelBase{
	protected static $table_name = 'accounts';
	protected static $primary_key = 'account_id';
	//
	protected static $auto_increment = true;
}