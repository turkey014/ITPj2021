<?php 
declare(strict_types=1);

// クロスサイトリクエストフォージェリ対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
// クリックジャギング対策
header('X-FRAME-OPTIONS: SAMEORIGINE');


require_once(__DIR__ . '/../libs/init.php');

// 送信ボタンクリックした後の処理
if(isset($_POST['email'])){
    // メールアドレスが空欄の場合
    if(empty($_POST['submit'])) {
        $errors['email'] = 'メールアドレスが未入力です。';
    }else{
        // POSTされたデータを変数に入れる
        $email = isset($_POST['email']) ? $_POST['email'] : NULL;
        
        // メールアドレス構文チェック
        if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])+([a-zA-Z0-9\._-]+)+$/",$email)){
            $errors['mail_check'] = "メールアドレスの形式が正しくありません。";
        }
        //DB確認
        $sql = "SELECT id FROM user WHERE email=:email";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':email', $email, PDO::PARAM_STR);
        
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        // users テーブルに同じメールアドレスがある場合、エラー表示
        if(isset($result["id"])){
            $errors['user_check'] = "このメールアドレスは既に利用されております。";
        }
    }
    // 登録されていない場合はinsert
    $sql = "INSERT INTO users(name, email, pass) VALUES (:name, :email, :pass)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':pass', $pass);
    $stmt->execute();
    
    // メールを送信する
    
    
    
    
    
    $msg = '会員登録が完了しました。';
    $link = '<a href="login.php">ログインページ</a>';
    
    
}




// memo
// login kinou
// https://note.com/koushikagawa/n/n9c6e396e2687
// https://qiita.com/ryo-futebol/items/5fb635199acc2fcbd3ff