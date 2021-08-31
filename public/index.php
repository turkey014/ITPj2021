<?php  // login.php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

// 入力データの取得
$params = [
    // カラム名 => validateパターン
    'name' => 'hogemusthoge', // XXX 存在しないvalidateパターンを記載
    'email' => 'must,email',
    'password' => 'must'
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
// in_array と isset と array_key_exists
// XXXX
    
    
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
if($data['password'] !== $data['password']){
    $error_messages[] = "パスワードが一致しませんでした。";
}

//var_dump($error_messages);


// エラーがあったら入力ページに戻す
if([] != $error_messages){
    $_SESSION['flash']['data'] = $data;
    $_SESSION['flash']['error_messages'] = $error_messages;
    // 入力ページに遷移
    header('Location: ./index.php');
    return;
}

$template_filename = 'index.twig';
$context = [];

require_once(BASEPATH . '/libs/fin.php');