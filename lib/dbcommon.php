<?php
/**
 * DB接続処理関連の関数定義ファイル
 *
 * @version 1.00
 * @author Tetsuhiro Koyama <te-koyama@usen.co.jp>
 * @license GNU Public License
 * @copyright Tetsuhiro Koyama
 * @package temp
 * @subpackage null
 */
ini_set('error_reporting', '1024');
ini_set('session.gc_maxlifetime', '900'); // セッション有効期限を秒数で指定する
ini_set('session.gc_divisor', 100);

/**
 * PHP内の処理をUTF8で行う（文字化け対策）
 */
mb_http_input('UTF-8');
mb_http_output('UTF-8');
mb_internal_encoding('UTF-8');

/**
 * PHP内の正規表現関数をUTF8で行う
 */
mb_regex_encoding('UTF-8');

/**
 * MySQL環境接続フラグ
 *
 * @param 2:本番環境(product)
 * @param 1:検証環境(staging)
 * @param 0:開発環境(develop)
 */
define("DB_CONNECT_FLAG", "0");

if (DB_CONNECT_FLAG == 2) {
    define("DBENV",  "本番環境");  // 環境名
    define("DBHOST", "dbhost");    // ホスト名
    define("DBNAME", "dbname");    // データベース名
    define("DBUSER", "dbuser");    // ユーザー名
    define("DBPASS", "dbpass");    // パスワード
} elseif (DB_CONNECT_FLAG == 1) {
    define("DBENV",  "検証環境");
    define("DBHOST", "dbhost");
    define("DBNAME", "dbname");
    define("DBUSER", "dbuser");
    define("DBPASS", "dbpass");
} else {
    define("DBENV",  "開発環境");
    define("DBHOST", "10.2.66.60");
    define("DBNAME", "manage");
    define("DBUSER", "manage");
    define("DBPASS", "manage");
}
?>
