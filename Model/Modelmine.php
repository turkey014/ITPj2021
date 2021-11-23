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
		$table_name = static::$table_name;
		$primary_key = static::$primary_key;
		$sql = "SELECT * FROM {$table_name} WHERE {$primary_key} = :user_id AND date BETWEEN :date_f AND :date_l";
		//var_dump($sql);
		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		/* メソッドをうまく使えていない */
		static::bindValues($pre, ['user_id' => $key]);
		static::bindValues($pre, ['date_f' => $day_f]);
		static::bindValues($pre, ['date_l' => $day_l]);
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
	
	public static function create(array $datum, string $flg) : ?static{
		
		// DBハンドルの取得
		$dbh = static::getDbHandle();
		$keys = array_keys($datum);
		static::checkColumn($keys);
		/* insertの発行 */
		// プリペアドステートメントの作成
		$table_name = static::$table_name;
		$primary_key = static::$primary_key;
		$sql = "INSERT INTO {$table_name}(`user_id`,`date`,`subject`, {$flg}, `created_at`) WHERE {$primary_key} = :user_id;";
	var_dump($flg);exit;

		$pre = $dbh->prepare($sql);
		
		// プレースホルダにバインド
		/* メソッドをうまく使えていない */
		static::bindValues($pre, ['user_id' => $key]);
		static::bindValues($pre, ['date_f' => $day_f]);
		static::bindValues($pre, ['date_l' => $day_l]);
		$r = $pre->execute();
		exit;
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
	protected static $table_name = 'registers';
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