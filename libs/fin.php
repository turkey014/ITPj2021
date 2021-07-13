<?php // index.php
declare(strict_types=1);

// バッファリングを終了する
ob_end_flush();

// 出力
echo $twig->render($template_filename, $context);