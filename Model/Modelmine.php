<?php
declare(strict_types=1);

Class Modelmine{
	
	public static function select_month($key, $ym) : ?static {
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		$day_f = $ym . '-01';
		$day_l = date('Y-m-d', strtotime('last day of ' . $ym));
		
		/* selectの発行 */
		// プリペアドステートメントの作成
		// $table_name = static::$table_name;
		$primary_key = static::$primary_key;
		$sql = "SELECT * FROM registers WHERE {$primary_key} = :user_id AND date BETWEEN :date_f AND :date_l";
		//var_dump($sql);
		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		/* メソッドをうまく使えていない */
		static::bindValues($pre, ['user_id' => $key, 'date_f' => $day_f, 'date_l' => $day_l]);
		
		$r = $pre->execute();
		$datum = $pre->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC(重複表示を省く);
		
		// keyに対応するデータがなければNULL return 
		if(false === $datum){
			return null;
		}
		
		// 取り出せたデータを「どこか」に格納する
		$obj = new static();
		foreach($datum as $k => $v){
			$obj->datum[$k] = $v;
		}
		return $obj;
	}
	
	public static function create(array $datum) : ?static{
		
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		//key(カラム群)の把握
		$keys = array_keys($datum);
		// カラム名のセキュリティチェック
		static::checkColumn($keys);
		//var_dump($keys);
		$keys_string = implode(', ', array_map(function($k) {
			return "`{$k}`";
		}, $keys));// XXX カラム名の``でエスケープ
		//
		$holder_keys = array_map(function($k){
			return ":{$k}";
		}, $keys);
		
		$holder_keys_string = implode(', ', $holder_keys);
		
		// insertの発行 
		// プリペアドステートメントの作成
		//$table_name = static::$table_name;
		$primary_key = static::$primary_key;
		$sql = "INSERT INTO registers ({$keys_string}) VALUES({$holder_keys_string});";
		
		//var_dump($keys_string,$holder_keys_string);exit;

		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		static::bindValues($pre, $datum);
		$r = $pre->execute();
		//var_dump($r);exit;
		
		/* tags tableに追加 */
		//直前のregist_idを取得
		//$regist_id = $dbh->lastInsertId();
		
		//static::tag_add($regist_id);
		// 入れたデータを格納
		$obj = new static();
		foreach($datum as $k => $v){
			$obj->datum[$k] = $v;
		}
		// もし「SERIAL(auto_increment)」ならIDを取得して格納する
		if(true === static::$auto_increment){
			$primary_key = static::$primary_key;
			$obj->datum[$primary_key] = $dbh->lastInsertId(); 
		}
		return $obj;
		
	}
	
	// tags に　insert する
	public static function tag_create(int $id, array $tags){
		
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		
		// insertの発行 
		// プリペアドステートメントの作成
		//直前のregist_idを取得
		$regist_id = $dbh->lastInsertId();
		$sql = "INSERT INTO tags (`regist_id`,`tag_name`,`user_id`) VALUES(:regist_id, :tag, :user_id);";
		//var_dump($sql);
		$pre = $dbh->prepare($sql);
		
		$pre->bindValue(":regist_id", $regist_id, \PDO::PARAM_INT);
		$pre->bindValue(":user_id", $id, \PDO::PARAM_INT);
		
		foreach($tags as $v){
			var_dump($v);
			// プレースホルダにバインド
			$pre->bindValue(":tag", $v, \PDO::PARAM_STR);
			$pre->execute();
		}
		//var_dump($r);exit;
		
	}
	// day_registers
	public static function day_registers($key, $date){
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		 
		/* selectの発行 */
		// プリペアドステートメントの作成
		// $table_name = static::$table_name;
		$primary_key = static::$primary_key;
		$sql = "SELECT * FROM registers WHERE {$primary_key} = :user_id AND date= :date;";
		//var_dump($sql);
		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		static::bindValues($pre, ['user_id' => $key]);
		static::bindValues($pre, ['date' => $date]);
		$r = $pre->execute();
		$datum = $pre->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC(重複表示を省く);
		//$datum = $pre->fetch(PDO::FETCH_ASSOC); //
		//var_dump($datum);
		// keyに対応するデータがなければNULL return 
		if(false === $datum){
			return null;
		}
		
		// 取り出せたデータを「どこか」に格納する
		$obj = new static();
		//var_dump($datum);
		foreach($datum as $k => $v){
			// 取得したregister_id でtagテーブルのデータを追加する
			$tags = static::get_tag($v["regist_id"]);
			$v['tags'] = $tags;
			
			$obj->datum[$k] = $v;
		}
		return $obj;
	}
	private static function get_tag(int $regist_id){
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		 
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = "SELECT tag_name FROM tags WHERE regist_id = :regist_id;";
		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		static::bindValues($pre, ['regist_id' => $regist_id]);
		$r = $pre->execute();
		$datum = $pre->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC(重複表示を省く);
		//$datum = $pre->fetch(PDO::FETCH_ASSOC); //
		//var_dump($datum);
		// keyに対応するデータがなければNULL return 
		if(false === $datum){
			return null;
		}
		$test = array_column($datum, "tag_name");
		return $test;
	}
	
	public function __get(string $name){
		//
		if(false === array_key_exists($name, $this->datum)){
			throw new \Exception("{$name}はありません");
		}
		return $this->datum[$name];
	}
	public function __set(string $name, $value){
		// カラム名チェックをやる
		$this::checkColumn([$name]);
		// カラム名が問題なければデータを入れる
		$this->datum[$name] = $value;
	}
	
	
	
	protected static function bindValues($pre, $data){
		foreach($data as $k => $v){
			if((true === is_int($v))||(true === is_float($v))){
				$type = \PDO::PARAM_INT;
			}else{
				$type = \PDO::PARAM_STR;
			}
			$pre->bindValue(":{$k}", $v, $type);
		}
	}
	
	// DBハンドルの取得
	protected static function getDbHandle(){
		return Db::getHandle();
	}
	
	protected static function checkColumn(array $keys){
		//
		foreach($keys as $k){
			$len = strlen($k);
			for($i = 0; $i < $len; ++$i){
				// 英数ならOK
				if(true === ctype_alnum($k[$i])){
					continue;
				}
				// アンダースコアはOK
				if('_' === $k[$i]){
					continue;
				}
				// else
				throw new \Exception("カラム名{$k}でダメっぽいの({$k[$i]})があったから処理やめる");
			}
		}
	}
	
	//
	protected static $auto_increment = false;
	protected $datum = [];
	
	// register テーブル用
	// protected static $table_name = 'registers';
	protected static $primary_key = 'user_id';
	
	// sums関数(自作)
	public static function sums($arr){
		$sums = ["inc" => 0, "spe" => 0];
		//var_dump($arr);
		foreach($arr->datum as $n){
			//var_dump($n);
			$sums["inc"] += $n["income"];
			$sums["spe"] += $n["spending"];
		}
		//var_dump($sums);
		return $sums;
	}
	
	public function array(){  // オブジェクトじゃないと返せないfindを配列に直す
		//var_dump($this->datum);
		$ar = (array)$this->datum;
		return $ar;
	}
	
}