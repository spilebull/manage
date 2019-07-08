<?php
// +------------------------------------------------------------------------+
// | index.php                                                              |
// +------------------------------------------------------------------------+
// | Copyright (c) Tetsuhiro Koyama 2008-2016. All rights reserved.         |
// | Version 1.00                                                           |
// | Last modified 08/04/2016                                               |
// | Email te-koyama@usen.co.jp                                             |
// +------------------------------------------------------------------------+

/**
 * Index
 *
 * @version 1.00
 * @author Tetsuhiro Koyama <te-koyama@usen.co.jp>
 * @license GNU Public License
 * @copyright Tetsuhiro Koyama
 * @package temp
 * @subpackage null
 */

/**
 * エラーメッセージをブラウザ表示
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');

/**
 * 共通ライブラリ取得
 */
include_once ("./lib/common.php");
include_once ("./lib/dbcommon.php");
include_once ("./lib/dbconnect.php");
include_once ("./lib/funccommon.php");

// ページにアクセスされたメソッド名が「POST」の場合
$gl_array = ($_SERVER['REQUEST_METHOD'] == "POST") ? _formstr($_POST) : _formstr($_GET);

session_cache_limiter('public'); // クライアント／プロキシのキャッシュを許可
session_start(); // セッション生成

/**
 * 実行順序(Do)
 */
f_db_connect(); // DB接続
f_show_header(); // ヘッダー表示
f_do_main(); // 画面切替処理
f_show_footer(); // フッター表示
f_db_close();
 // DB切断

/**
 * 画面切替処理(Switch)
 */
function f_do_main()
{
    global $gl_array; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    switch ($mode) { // 「$mode」で画面表示切替
        case "search": // 「$mode」が「""(初期)」場合、一覧画面へ
            f_show_list();
            break;
        case "import": // 「$mode」が「import」場合、登録画面へ
            f_do_import();
            break;
        case "export": // 「$mode」が「export」場合、登録画面へ
            f_do_export();
            break;
        case "insert": // 「$mode」が「insert」の場合、登録画面へ
            f_show_insert();
            break;
        case "do_insert": // 「$mode」が「do_insert」の場合、新規作成処理へ
            f_do_insert();
            break;
        case "update": // 「$mode」が「update」の場合、更新画面へ
            f_show_update();
            break;
        case "do_update": // 「$mode」が「do_update」の場合、更新処理へ
            f_do_update();
            break;
        case "do_delete": // 「$mode」が「do_delete」の場合、削除処理へ
            f_do_delete();
            break;
        default: // 「$mode」が「""(初期)」場合、一覧画面へ
            f_show_list();
            break;
    }
}

/**
 * 一覧画面(View)
 */
function f_show_list()
{
    global $gl_array; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    if (empty($search)) {
        // DB一覧取得処理
        $where .= " WHERE email LIKE '%" . $search . "%'"; // 検索キーワード
    } else {
        // DB一覧取得処理
        $where = "";
    }
    // DB一覧取得処理
    $sql = 'SELECT * FROM tests'; // レコード一覧取得
    $sql .= $where; // レコード一覧取得
    $sql .= ' ORDER BY id ASC'; // ID降順

    $records = f_db_query($sql); // SQL実行

    /**
     * レコードデータ取得
     */
    $results[] = array();
    foreach ($records as $record) {
        $results[] = array(
            'id' => $record['id'],
            'email' => $record['email']
        );
    }

    ?>
<body>
  <table>
    <tr>
      <form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
        <td><input type="text" name="search" value="" /></td>
        <td>
          <input type="submit" name="submit" value="検索" width="275" height="48" />
          <input type="hidden" name="mode" value="search" />
        </td>
      </form>
      <form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
        <td>
          <input type="submit" name="submit" value="登録" width="275" height="48" />
          <input type="hidden" name="mode" value="insert" />
        </td>
      </form>
      <form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
        <td>
          <input type="submit" name="submit" value="CSV" width="275" height="48" />
          <input type="hidden" name="mode" value="export" />
        </td>
      </form>
    </tr>
  </table>

  <table border="1" width="100%" height="30%">
    <tr>
      <th>ID</th>
      <th>メール</th>
      <th>変更</th>
      <th>削除</th>
    </tr>
    <?php for($i = 1; $i < count($results); $i++){ ?>
      <tr>
        <td><?=$results[$i]['id']?></td>
        <td><?=$results[$i]['email']?></td>
        <!-- メソッド名"POST" $_SERVER['PHP_SELF'](自身ファイルへ送信) -->
        <form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
          <td>
            <input type="hidden" name="mode" value="update" />
            <input type="hidden" name="id" value="<?=$results[$i]['id']?>" />
            <input type="submit" name="submit" value="変更" />
          </td>
        </form>
        <form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="return confirm('削除しますがよろしいですか');">
          <td>
            <input type="hidden" name="mode" value="do_delete">
            <input type="hidden" name="id" value="<?=$results[$i]['id']?>" />
            <input type="submit" name="submit" value="削除">
          </td>
        </form>
      </tr>
    <?php } ?>
  </table>
<?php
}

/**
 * 登録画面(View)
 */
function f_show_insert()
{
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
  <table border="1" width="100%">
    <tr>
      <th>メールアドレス</th>
      <td colspan="2"><input type="text" name="email" value="" /></td>
    </tr>
    <tr>
      <td><input type="button" onClick="history.back();" value="戻る" /></td>
      <td>
        <input type="submit" name="submit" value="登録" />
        <input type="hidden" name="mode" value="do_insert" />
      </td>
    </tr>
  </table>
</form>

<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
  <table>
    <tr>
      <td><input type="file" name="file" value="" />
      <td>
        <input type="submit" name="submit" value="CSV" width="275" height="48" />
        <input type="hidden" name="mode" value="import" />
      </td>
    </tr>
  </table>
</form>
<?php
}

/**
 * 登録処理(Model)
 */
function f_do_insert()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    /**
     * INSERT処理
     */
    $sql = "INSERT INTO tests ("; // DB登録処理
    $sql .= "email "; // メールアドレス
    $sql .= ") VALUES (";
    $sql .= ":email";
    $sql .= ")";

    $insert = $dbh->prepare($sql);
    $insert->bindParam(':email', $email, PDO::PARAM_STR);

    $flg = $insert->execute(); // SQL実行
    $flg ? f_show_mes("登録完了") : f_show_mes("登録失敗");
    return; // メッセージ表示
}

/**
 * 更新画面(View)
 */
function f_show_update()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    if ($id != "") {
        // 選択されたレコード取得
        $sql = "SELECT * FROM tests";
        $sql .= " WHERE id = '" . $id . "'";

        $records = f_db_query($sql); // SQL実行

        /**
         * レコードデータ取得
         */
        $results[] = array();
        foreach ($records as $record) {
            $results = array(
                'id' => $record['id'],
                'email' => $record['email']
            );
        }
    } else {
        f_show_mes("IDが不正です"); // エラーメッセージ
        return;
    }
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
  <table border="1" width="100%">
    <tr>
      <th>ID</th>
      <td><?=$results['id']?></td>
    </tr>
    <tr>
      <th>メールアドレス</th>
      <td><input type="text" name="email" value="<?=$results['email']?>" /></td>
    </tr>
    <tr>
      <!--更新処理-->
      <td><input type="button" onClick="history.back();" value="戻る" /></td>
      <td>
        <input type="hidden" name="mode" value="do_update" />
        <input type="hidden" name="id" value="<?=$results['id']?>" />
        <input type="submit" name="submit" value="更新" />
      </td>
    </tr>
  </table>
</form>
<?php
}

/**
 * 更新処理(Model)
 */
function f_do_update()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    /**
     * UPDATE処理
     */
    $sql .= "UPDATE tests SET ";
    $sql .= "email     = '" . $email . "'";
    $sql .= " WHERE id = '" . $id . "'";

    $update = $dbh->prepare($sql);

    $flg = $update->execute(); // SQL実行
    $flg ? f_show_mes("更新完了") : f_show_mes("更新失敗"); // メッセージ表示
}

/**
 * 削除処理
 */
function f_do_delete()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    /**
     * DELETE処理
     */
    if ($id != "") {
        // 選択されたレコード取得
        $sql = "DELETE FROM tests";
        $sql .= " WHERE id ='" . $id . "'";

        $delete = $dbh->prepare($sql);

        $flg = $delete->execute(); // SQL実行
        $flg ? f_show_mes("削除完了") : f_show_mes("削除失敗"); // メッセージ表示
    } else {
        f_show_mes("IDが不正です"); // エラーメッセージ
        return;
    }
}

/**
 * ファイルインポート処理
 */
function f_do_import()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    try {
        // ファイルを読み込んでCSVとして読み取るモードに設定
        $filename = $_FILES['file']['tmp_name'];
        $file = new SplFileObject($filename);
        $file->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE | SplFileObject::READ_CSV);

        // データベースに接続して例外スローモードに設定
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "REPLACE INTO tests ("; // DB登録処理
        $sql .= "id, "; // ID
        $sql .= "email"; // メールアドレス
        $sql .= ") VALUES (";
        $sql .= "?, ";
        $sql .= "?";
        $sql .= ")";

        // プリペアドステートメントを生成
        $insert = $dbh->prepare($sql);

        // 1行ずつ処理
        foreach ($file as $row) {
            $flg = $insert->execute($row);
        }

        $flg ? f_show_mes("登録完了") : f_show_mes("登録失敗"); // メッセージ表示
    } catch (RuntimeException $e) {
        // エラー発生時はメッセージ出力
        echo 'Error: ' . $e->getMessage() . PHP_EOL;
    }
}

/**
 * ファイルエクスポート処理
 * @see CSVダウンロード
 */
function f_do_export()
{
    global $gl_array, $dbh; // 関数の中で関数の外の変数／配列を使用する場合に宣言
    extract($gl_array); // 配列データ名を変数名へ置換

    // ファイル名用の設定
    $todate = date("Ymd_His");

    // 以下を記載すると直接ダウンロード
    header("Content-Type: text/csv;");
    header("Content-Disposition: attachment; filename=$todate.csv");

    //DBから値を抽出
    $sql = "SELECT * From tests";
    $download = $dbh->query($sql);
    // エラー処理
    if (!$download) {
        $info = $dbh->errorInfo();
        exit($info[2]);
    }
    $download->execute();

    $csvlist = "ID,メールアドレス\r\n";
    while (($result = $download->fetch(PDO::FETCH_ASSOC)) !== false) {
        $csvlist .= $result['id'] . ",";
        $csvlist .= $result['email'] . "\r\n";
    }
    echo mb_convert_encoding($csvlist, "SJIS-win", "UTF-8");
}

/**
 * Messeage
 */
function f_show_mes($str, $history_back = null)
{
?>
<div class="flame_left_m">
  <br />
  <br />
  <font color="red"><?=$str?></font><br />
  <form id="form" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="mode" value="" />
    <p class="button07">
      <?php if($history_back == null){ ?>
        <input type="submit" value="戻る" alt="戻る" width="76" height="29">
      <?php }else{ ?>
        <input type="button" value="戻る" alt="戻る" onClick="history.back();">
      <?php } ?>
    </p>
  </form>
</div>
<?php
}

/**
 * Header
 */
function f_show_header()
{
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Manage</title>
<link href="./css/*.css" rel="stylesheet" type="text/css" />
</head>
<?php
}

/**
 * Footer
 */
function f_show_footer()
{
?>
<div id="footer">
  <noscript>Copyright&copy; 株式会社USEN All rights reserved</noscript>
</div>
</body>
</html>
<?php
}