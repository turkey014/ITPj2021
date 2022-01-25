<?php
declare(strict_types=1);

Class Modelmine{
	/*
	該当月のデータを配列で返す
	@param $ym string 年月('YYYY-MM')
	*/
	public static function select_mon($ym){
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = 'SELECT * FROM registers 
				WHERE user_id = :user_id 
				AND date BETWEEN :date_f AND :date_l';
		
		/* 実際はセッションから代入 */
		$user_id = 2;
		
		$date_f = $ym . '-01';
		$date_l = date('Y-m-d', strtotime('last day of ' . $ym));
		$bind = []; // 'プレースホルダ名' = 変数
		$bind['user_id'] = $user_id;
		$bind['date_f'] = $date_f;
		$bind['date_l'] = $date_l;
		
		// DBハンドルの取得
		$pre = Db::getHandle()->prepare($sql);
		static::bindValues($pre, $bind);
		$r = $pre->execute();
		//
		$list = $pre->fetchAll(\PDO::FETCH_ASSOC);
		return $list;
	}
	
	/*
	該当日のデータを配列で返す
	@param $ymd string 年月日('YYYY-MM-DD')
	*/
	public static function select_day($ymd){
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = 'SELECT * FROM registers 
			WHERE user_id = :user_id AND date = :day';
		/* 実際はセッションから代入 */
		$user_id = 2;
		$bind = []; // 'プレースホルダ名' = 変数
		$bind['user_id'] = $user_id;
		$bind['day'] = $ymd;
		
		// DBハンドルの取得
		$pre = Db::getHandle()->prepare($sql);
		static::bindValues($pre, $bind);
		$r = $pre->execute();
		//
		$list = $pre->fetchAll(\PDO::FETCH_ASSOC);
		// keyに対応するデータがなければNULL return 
		if(false === $list){
			return null;
		}
		// 'tags'を追加
		$lists = static::tagadd($list);
		return $lists;
	}
	/*
	@param $limit_num int 一ページ当たりの最大データ表示数
	@param $p int 現在の表示ページ数
	@param $sort string ソート条件
	@param $amount_flg string 収支フラグ
	*/
	public static function select_f($limit_num, $p, $sort, $amount_flg){
		/* SQLとクエリストリングを動的に作るための情報作成 */
		$where = []; // where句の条件
		$bind = []; // プレースホルダ名 => バインドする値
		$search = []; //url に検索条件を引き継ぐ為
		//収支の判定および条件
		if($amount_flg === 'income'){
			$where[] = 'income IS NOT NULL';
		}elseif($amount_flg === 'spending'){
			$where[] = 'spending IS NOT NULL';
		}else{
			// 
			throw new \Exception('$amount_flagがおかしいです');
		}
		// sort条件のホワイトリスト
		$sort_list = [
			// 外部パラメタの値 => SQL の ORDER BYに渡す文字列,
			'date' => 'date',
			'date_desc' => 'date DESC',
			'subject' => 'subject',
			'subject_desc' => 'subject DESC',
		];
			
		// この条件は確定
		$where[] = 'user_id = :user_id';
		$user_id = 2;
		$bind['user_id'] = $user_id;
		
		$bind['limit_num'] = $limit_num + 1;
		$bind['offset_num'] = $limit_num * ($p - 1);
		
		/* 検索用項目の取得 */
		// 期間
		$from_date = strval($_GET['from_date'] ?? '');
		if('' !== $from_date){
			$where[] = 'date >= :from_date';
			$bind['from_date'] = $from_date;
			$search[] = 'from_date=' . rawurlencode($from_date);
		}
		$to_date = strval($_GET['to_date'] ?? '');
		if('' !== $to_date){
			$where[] = 'date <= :to_date';
			$bind['to_date'] = $to_date;
			$search[] = 'to_date=' . rawurlencode($to_date);
		}
		
		// 科目名(部分一致)
		$subject_search = strval($_GET['subject_search'] ?? '');
		if('' !== $subject_search){
			// XXX
			$where[] = 'subject LIKE :subject';
			$bind['subject'] = "%{$subject_search}%";
			$search[] = 'subject_search=' . rawurlencode($subject_search);
		}
		
		// WHERE句の文字列を作成
		$where_string = implode(' AND ', $where);
		
		// クエリストリングを作成
		$search_string_e = '';
		if([] !== $search){
			$search_string_e = implode('&', $search);
		}
		// ソート条件デフォルトはdate, regist_id
		$sort_string = $sort_list[$sort] ?? 'date DESC, regist_id';
		
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = 'SELECT * FROM registers
				 WHERE  ' . $where_string . '
				 ORDER BY ' . $sort_string . '
				 LIMIT :limit_num OFFSET :offset_num;';
			//var_dump($bind['amount_flg']);
			
		// DBハンドルの取得
		$pre = Db::getHandle()->prepare($sql);
		// プレースホルダにバインド
		static::bindValues($pre, $bind);
		//
		$r = $pre->execute();
		//
		$list = $pre->fetchAll(\PDO::FETCH_ASSOC);
		
		// 'tags'を追加
		$lists = static::tagadd($list);
		
		return [
			'list' => $lists,
			'search_string_e' => $search_string_e,
			// 以下、formからの入力パラメータ
			'from_date' => $from_date,
			'to_date' => $to_date,
			'subject_search' => $subject_search,
		];
	}
	// 全収入支出取得
	public static function select_all(){
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = 'SELECT `income`, `spending` FROM registers 
				WHERE user_id = :user_id';
		
		/* 実際はセッションから代入 */
		$user_id = 2;
		$bind = []; // 'プレースホルダ名' = 変数
		$bind['user_id'] = $user_id;
		// DBハンドルの取得
		$pre = Db::getHandle()->prepare($sql);
		static::bindValues($pre, $bind);
		$r = $pre->execute();
		//
		$list = $pre->fetchAll(\PDO::FETCH_ASSOC);
		return $list;
	}
	
	/* registers から取得したデータにtagsのデータを付随する */
	private static function tagadd(array $list){
		/* selectの発行 */
		// プリペアドステートメントの作成
		$sql = "SELECT tag_name FROM tags WHERE regist_id = :regist_id;";
		// DBハンドルの取得
		$pre = Db::getHandle()->prepare($sql);
		foreach($list as $k => $v){
			// プレースホルダにバインド
			static::bindValues($pre, ['regist_id' => $v['regist_id']]);
			$r = $pre->execute();
			$datum = $pre->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC(重複表示を省く);
			// keyに対応するデータがなければNULL return 
			if(false === $datum){
				return null;
			}
			$tag = array_column($datum, "tag_name");
			$v['tags'] = $tag;
			$list[$k] = $v;
		}
		return $list;
	}
	
	/* 
	データ入力 
	@param $datum array ['カラム名' => 'パラメータ']
	*/
	public static function create(array $datum){
		//key(カラム群)の把握
		$keys = array_keys($datum);
		// カラム名のセキュリティチェック
		static::checkColumn($keys);
		// カラム名の``でエスケープ
		$keys_string = implode(', ', array_map(function($k) {
			return "`{$k}`";
		}, $keys));
		
		//プレースホルダを作成
		$holder_keys = array_map(function($k){
			return ":{$k}";
		}, $keys);
		
		$holder_keys_string = implode(', ', $holder_keys);
		// (':key1',':key2',':key3')みたいな感じ
		
		// insertの発行 
		// プリペアドステートメントの作成
		// 入力するデータによらない作り(今回はいらない気もする)(もしくはtagテーブルとの共通化)
		$sql = "INSERT INTO registers ({$keys_string}) VALUES({$holder_keys_string});";
		$pre = Db::getHandle()->prepare($sql);
		static::bindValues($pre, $datum);
		
		$r = $pre->execute();
		//var_dump($r);
		//return $r;
	}
	
	/* 
	tags に　insert する
	@param $id int user_id
	@param $tags array 登録タグを配列でn個 
	*/ 
	public static function tag_create(int $id, array $tags){
		// DBハンドルの取得
		$dbh = Db::getHandle();
		// insertの発行 
		// プリペアドステートメントの作成
		$sql = "INSERT INTO tags (`regist_id`,`tag_name`,`user_id`) VALUES(:regist_id, :tag_name, :user_id);";
		// DBハンドルの取得
		$pre = $dbh->prepare($sql);
		
		$bind = []; // 'プレースホルダ名' = 変数
		//直前のregist_idを取得
		$bind['regist_id'] = $dbh->lastInsertId();
		$bind['user_id'] = $user_id;
		static::bindValues($pre, $bind);
		
		foreach($tags as $v){
			// プレースホルダにバインド
			static::bindValues($pre, ['tag_name' => $v]);
			$pre->execute();
		}
	}
	
	/* $data['プレースホルダ名' => 変数] をバインドする */
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
	
	/* 
	与えられたデータの収支別の合計を返す
	@param $arr array 
	*/
	public static function sums($arr){
		$sums = ["inc" => 0, "spe" => 0];
		foreach($arr as $d){
			//var_dump($n);
			$sums["inc"] += $d["income"];
			$sums["spe"] += $d["spending"];
		}
		//var_dump($sums);
		return $sums;
	}
	// アクセス不能なプロパティからデータを読み込もうとしたとき..?
	public function __get(string $name){
		//
		if(false === array_key_exists($name, $this->datum)){
			throw new \Exception("{$name}はありません");
		}
		return $this->datum[$name];
	}
	// アクセス不能なプロパティにデータを格納しようとしたとき..?
	public function __set(string $name, $value){
		// カラム名チェックをやる
		$this::checkColumn([$name]);
		// カラム名が問題なければデータを入れる
		$this->datum[$name] = $value;
	}
	
	/*
	カラムチェック(英数またはアンダースコア以外の文字を使っていたらはじく)
	@param $keys array 
	*/
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
	
}