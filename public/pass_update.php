<?php // pass_update.php
declare(strict_types=1);

//
require_once(__DIR__ . '/../libs/init.php');
// パスワードの変更 UPDATE users SET password = new_password WHERE user_id = :user_id
// 入力されたパスワードが正しいか確認する
// 確認用パスワードと新パスワードが一致するか確認
