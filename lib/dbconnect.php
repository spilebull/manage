<?php
/**
 * DB接続操作共通関数ファイル
 *
 * @version 1.00
 * @author Tetsuhiro Koyama <te-koyama@usen.co.jp>
 * @license GNU Public License
 * @copyright Tetsuhiro Koyama
 * @package temp
 * @subpackage null
 */

/**
 * f_db_connect <DB接続処理>
 *
 * @param
 * @return
 */
function f_db_connect()
{
    global $dbh;

    // DB接続設定
    $host     = DBHOST;
    $database = DBNAME;
    $user     = DBUSER;
    $password = DBPASS;

    // DB接続
    $dsn = 'mysql:dbname='.$database.';host='.$host.'';
    try{
        $dbh = new PDO($dsn, $user, $password);

        if ($dbh != TRUE) {
            die("MySQL への接続に失敗しました。<br />");
        }
        $dbh->query('SET NAMES utf8');

    }catch (PDOException $e){
        print('Error:'.$e->getMessage());
        die();
    }
}

/**
 * f_db_close <DB切断処理>
 * @param
 * @return
 */
function f_db_close()
{
    global $dbh;

    // DB切断
    $dbh = null;
    if (!empty($dbh)) {
        die("MySQL の切断に失敗しました。<br />");
    }
}

/**
 * f_db_connect_info_print <DB接続情報表示>
 * @param
 * @return
 */
function f_db_connect_info_print()
{
    $env  = DBENV;
    $host = DBHOST;
    $user = DBUSER;
    $database = DBNAME;

    print "<table border='1'>";
    print "  <tr><td>環境</td><td>" . $env . "</td></tr>";
    print "  <tr><td>Server</td><td>" . $host . "</td></tr>";
    print "  <tr><td>User</td><td>" . $user . "</td></tr>";
    print "  <tr><td>DbName</td><td>" . $database . "</td></tr>";
    print "</table>";
}

/**
 * f_db_query <SQL実行>
 * @see ＜例＞SELECT文, INSERT文, UPDATE文, DELETE文など
 * @param SQL文
 * @return クエリー結果リソースまたはTRUE
 */
function f_db_query($pa_Sql)
{
    global $dbh;

    // SQL実行
    $lo_Result = $dbh->prepare($pa_Sql);
    $lo_Result->execute();

    if ($lo_Result == FALSE) {
        die("エラー番号：" . $dbh->errorCode(). "<br />エラー内容：" . $dbh->errorInfo() . "<br />");
    }
    return $lo_Result;
}

/**
 * f_db_count <DBから指定条件に一致するレコード数を取得>
 * @param $pa_from  : FROM句 テーブル名
 * @param $pa_where : WHERE句(省略したい時は "" を指定する。)
 * @return 件数
 */
function f_db_count($pa_from, $pa_where)
{
    global $dbh;

    $lo_Sql = " SELECT COUNT(*) AS cnt FROM " . $pa_from;
    if ($pa_where != "") {
        $lo_Sql .= "    WHERE " . $pa_where;
    }
    $lo_rs = f_db_query($lo_Sql);    // SQL実行
    $lo_aryFld = f_db_fetch($lo_rs);
    return $lo_aryFld["cnt"];
}

/**
 * f_db_fetch <フェッチ>
 * @see DBから取得した値をすべてShift-Jisへ変換
 * @param クエリー結果リソース
 * @return クエリー結果を含む配列。行がない場合はFALSE
 */
function f_db_fetch($pa_Result)
{
    $lo_fetch_array = mysql_fetch_array($pa_Result);
    return $lo_fetch_array;
}

// ==========================================================================
// 　関数名　：　f_db_num_rows
// 　機　能　：　クエリー結果のレコード数を取得
// 　引　数　：　クエリー結果リソース
// 　戻り値　：　クエリー結果に含まれるレコード数。
// 　　　　　：　エラーの場合はFALSE
// ==========================================================================
function f_db_num_rows($pa_Result)
{
    $lo_num_rows = mysql_num_rows($pa_Result);
    return $lo_num_rows;
}

// ==========================================================================
// 　関数名　：　f_db_num_fields
// 　機　能　：　クエリー結果のフィールド数を取得
// 　引　数　：　クエリー結果リソース
// 　戻り値　：　クエリー結果に含まれるフィールド数。
// 　　　　　：　エラーの場合はFALSE
// ==========================================================================
function f_db_num_fields($pa_Result)
{
    $lo_num_rows = mysql_num_fields($pa_Result);
    return $lo_num_rows;
}

// ==========================================================================
// 　関数名　：　f_db_insert_id
// 　機　能　：　挿入したレコードのIDを取得する。
// 　　　　　：　（直前でINSERTしたレコードのAUTO_INCREMENTフィールドの値）
// 　引　数　：　なし
// 　戻り値　：　なし
// ==========================================================================
function f_db_insert_id()
{
    global $dbh;
    $lo_insert_id = mysql_insert_id($dbh); // SQL実行（SQLクエリーを実行）
    return $lo_insert_id;
}

// ==========================================================================
// 　関数名　：　f_db_lookup
// 　機　能　：　DBから指定されたフィールドを値を取得する。
// 　　　　　：　該当のデータが1件になるようにWHERE句を指定する。
// 　　　　　：　該当のデータが複数件合った時は最初に取得したデータが返却される。
// 　引　数　：　$pa_field : フィールド名
// 　　　　　：　$pa_from : FROM句 テーブル名
// 　　　　　：　$pa_where : WHERE句(省略したい時は "" を指定する。)
// 　戻り値　：　クエリー結果を含む配列
// ==========================================================================
function f_db_lookup($pa_field, $pa_from, $pa_where)
{
    global $dbh;
    $lo_Sql = " SELECT " . $pa_field . " FROM " . $pa_from;
    if ($pa_where != "") {
        $lo_Sql .= "    WHERE " . $pa_where;
    }
    $lo_rs = f_db_query($lo_Sql); // SQL実行（SQLクエリーを実行）
    $lo_aryFld = f_db_fetch($lo_rs);
    return $lo_aryFld;
}

/**
 * f_db_insert <指定テーブルへ配列でデータ追加>
 * @param $pa_field : 更新するデータが格納されている配列
 * @param $pa_field : 配列のkey = フィールド名
 * @param $pa_field : 配列のvalue = 設定する値
 * @param $pa_from : FROM句 テーブル名
 * @return クエリー結果を含む配列
 */
function f_db_insert($pa_field, $pa_from)
{
    $lo_SetKey = "";
    $lo_SetValue = "";
    $lo_Sql = " INSERT INTO " . $pa_from;
    foreach ($pa_field as $key => $value) {
        if ($lo_SetKey != "") {
            $lo_SetKey .= ",";
            $lo_SetValue .= ",";
        }
        $lo_SetKey .= $key;
        $lo_SetValue .= $value;
    }
    $lo_Sql .= "(" . $lo_SetKey . ") VALUES (" . $lo_SetValue . ")";

    $lo_rs = f_db_query($lo_Sql); // SQL実行（SQLクエリーを実行）
}

// ==========================================================================
// 　関数名　：　f_db_delete
// 　機　能　：　指定されてテーブルの指定されてデータを削除する。
// 　引　数　：　$pa_from : FROM句 テーブル名
// 　　　　　：　$pa_where : WHERE句(省略したい時は "" を指定する。)
// 　戻り値　：　クエリー結果を含む配列
// ==========================================================================
function f_db_delete($pa_from, $pa_where)
{
    $lo_Sql = " DELETE FROM " . $pa_from;
    if ($pa_where != "") {
        $lo_Sql .= " WHERE " . $pa_where;
    }
    $lo_rs = f_db_query($lo_Sql); // SQL実行（SQLクエリーを実行）
}

// ==========================================================================
// 　関数名　：　f_db_update
// 　機　能　：　指定されてテーブルの指定されてデータを配列に設定されている
// 　　　　　：　データで更新する。
// 　引　数　：　$pa_field : 更新するデータが格納されている配列
// 　　　　　：　　　　　　　　 : 配列のkey = フィールド名
// 　　　　　：　　　　　　　　 : 配列のvalue = 設定する値
// 　　　　　：　$pa_from : FROM句 テーブル名
// 　　　　　：　$pa_where : WHERE句(省略したい時は "" を指定する。)
// 　戻り値　：　クエリー結果を含む配列
// ==========================================================================
function f_db_update($pa_field, $pa_from, $pa_where)
{
    $lo_Set = "";
    $lo_Sql = " UPDATE " . $pa_from;
    foreach ($pa_field as $key => $value) {
        if ($lo_Set != "") {
            $lo_Set .= ",";
        }
        $lo_Set .= $key . "=" . $value;
    }
    $lo_Sql .= " SET " . $lo_Set;
    if ($pa_where != "") {
        $lo_Sql .= " WHERE " . $pa_where;
    }

    $lo_rs = f_db_query($lo_Sql); // SQL実行（SQLクエリーを実行）
}

// ==========================================================================
// 　関数名　：　_sqlstr
// 　機　能　：　データをSQL用に変換
// 　引　数　：　変換する文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _sqlstr($string)
{
    // バックスラッシュを付加する
    $string = addslashes($string);
    return $string;
}
?>
