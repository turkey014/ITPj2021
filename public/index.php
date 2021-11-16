<?php  // index.php
declare(strict_types=1);

require_once(__DIR__ . '/../libs/init.php');

$session = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

// CSRF用のtoken作成
$token = bin2hex(random_bytes(32));
$_SESSION['users']['csrf_token'] = $token;

$template_filename = 'index.twig';
$context = [
    'login_email' => $session['email'] ?? '',
    'login_error' => $session['error'] ?? false,
    'csfr_token' => $token,
];

require_once(BASEPATH . '/libs/fin.php');