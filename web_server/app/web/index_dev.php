<?php
/**
 * 開発用フロントコントローラー
 * .htaccessの設定で、リクエストは基本的にすべてこのindex.phpに
 * アクセスするようにり、bootstrap.phpの読み取りが行われる。
 */
require '../bootstrap.php';
require '../MiniBlogApplication.php';

$app = new MiniBlogApplication(true);
$app->run();