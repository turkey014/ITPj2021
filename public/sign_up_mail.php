<?php 
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

// 入力データの取得
$params = [
    // カラム名 => validateパターン
    'name' => 'hogemusthoge', // XXX 存在しないvalidateパターンを記載
    'email' => 'must,email',
    'password' => 'must',
    'password2' => 'must',
];
$data = [];
foreach($params as $col_name => $v){
    $data[$col_name] = strval($_POST[$col_name] ?? '');
}
//var_dump($data);

// validate
$error_messages = [];
foreach($params as $col_name => $v){
    // validateパターンを配列にする
    $validates = explode(',', $v); // explode() 文字列を文字列で分割
    $validates = array_map(function($v){  // array_map() 指定した配列の要素にコールバック関数を適用する
        return strtolower(trim($v)); // trim() 文字列の先頭および末尾にあるホワイトスペースを取り除く
    }, $validates);
//var_dump($validates); continue;
    
    // 必須入力のチェック(email,password,password2)
    if(true === in_array('must', $validates, true)){
        if('' === $data[$col_name]){
            $error_messages[] = "{$col_name}が未入力です。";
        }
    }
    // emailアドレスのチェック
    if(true === in_array('email', $validates, true)){
        if(false === filter_var($data[$col_name], FILTER_VALIDATE_EMAIL)){
            $error_messages[] = "{$col_name}のフォーマットで入力してください。";
        }
    }
}
// パスワードの確認
if($data['password'] !== $data['password2']){
    $error_messages[] = "パスワードが一致しませんでした。";
}

//var_dump($error_messages);


// エラーがあったら入力ページに戻す
if([] != $error_messages){
    $_SESSION['flash']['data'] = $data;
    $_SESSION['flash']['error_messages'] = $error_messages;
    // 入力ページに遷移
    header('Location: ./sign_up.php');
    return;
}

// XXX ここまで来たらデータOKなのでユーザ登録する

// DBへの登録(insert)
$dbh = Db::getHandle();

$r = $dbh->beginTransaction(); // XXX

// usersへのinsert
// 準備された文（プリペアドステートメント）の用意
$sql = 'insert into users(user_name, password, created_at, updated_at) values(:user_name, :password, :created_at, :updated_at);';
$pre = $dbh->prepare($sql);
//var_dump($pre);

// プレースホルダに値をバインド
$pre->bindValue(':user_name', $data['name']);
$pre->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
$now_date_string = date('Y-m-d H:i:s');
$pre->bindValue(':created_at', $now_date_string);
$pre->bindValue(':updated_at', $now_date_string);

// SQLを実行
$r = $pre->execute();
//var_dump($r);
// user_idを把握
$user_id = $dbh->lastInsertId();
// var_dump($user_id);

// アクティベーションのDB登録とemail送信
$activation_token = bin2hex(random_bytes(64));
//var_dump($activation_token);
$sql = 'insert into activations(activation_token, user_id, email, activation_ttl, created_at) values(:activation_token, :user_id, :email, :activation_ttl, :created_at);';
$pre = $dbh->prepare($sql);
//var_dump($pre);

// プレースホルダに値をバインド
$pre->bindValue(':activation_token', $activation_token);
$pre->bindValue(':user_id', $user_id);
$pre->bindValue(':email', $data['email']);
$pre->bindValue(':activation_ttl', date('Y-m-d H:i:s', time() + 86400));
$pre->bindValue(':created_at', $now_date_string);

// SQLを実行
$r = $pre->execute();

// COMMIT
$dbh->commit();


// email送信
// ここらへんでエラーはいてたら composer.phar require swiftmailer/swiftmailer してないかも
// Create the Transport
$transport = new Swift_SmtpTransport('localhost',25);
// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// メールの本文を作成
$body = $twig->render('email/sign_up.twig', ['activation_token' => $activation_token]);

// Create a message
$message = (new Swift_Message('ユーザー登録用アクティべーションメール'))
    ->setFrom(['register@dev2.m-fr.net' => 'register'])  // どういう処理なのか後で質問するor調べる
    ->setTo($data['email'])
    ->setBody($body)
    ;

var_dump($message); 
// XXX 今はメールを送らないので、確認用に↓↓↓のコード
$_SESSION['activation_token'] = $activation_token; // XXX 今はemailで送らないのでデバッグ用です。

/* Send the message
$result = $mailer->send($message);
var_dump($result);
*/

// 完了画面に遷移
header('Location: ./sign_up_fin_print.php');