<?php
declare(strict_types=1);
Class ModelBase{
	
	//select * form table where id=XX;
	// $obj = Class名::find(key);
	// // 使うとき
	// echo $obj->hoge;
	// echo $obj->foo;
	// カラム名のチェック
	
	public static function find($key, $day_f, $day_l) : ?static {
			// DBハンドルの取得
			$dbh = static::getDbHandle();
			 
			/* selectの発行 */
			// プリペアドステートメントの作成
			$table_name = static::$table_name;
			$primary_key = static::$primary_key;
//			$sql = "SELECT * FROM {$table_name} WHERE {$primary_key} = :id ;";
			$sql = "SELECT * FROM {$table_name} WHERE {$primary_key} = :user_id AND date BETWEEN :date_f AND :date_l";
			//var_dump($sql);
			$pre = $dbh->prepare($sql);
			
			// プレースホルダにバインド
			static::bindValues($pre, ['user_id' => $key]);
			static::bindValues($pre, ['date_f' => $day_f]);
			static::bindValues($pre, ['date_l' => $day_l]);
			// SQLを実行
			//var_dump($pre, $key, $day_f, $day_l);
			$r = $pre->execute();
			$datum = $pre->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC(重複表示を省く);
			//$datum = $pre->fetch(PDO::FETCH_ASSOC); //
//			var_dump($datum);exit;
			// keyに対応するデータがなければNULL return 
			if(false === $datum){
				return null;
			}
			
			// 取り出せたデータを「どこか」に格納する
			$obj = new static();
			foreach($datum as $k => $v){
				$obj->datum[$k] = $v;
			}
			//var_dump($obj);
			//$obj->datum[カラム名] = データ;
			return $obj;
	}
	
	public function __get(string $name){
		//
		var_dump($name,$this->datum);
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
				throw new \Exception("カラム名{$k}でダメ臭いの({$k[$i]})があったから処理やめる");
			}
		}
		
	}
	
	//
	protected static $auto_increment = false;
	protected $datum = [];
}