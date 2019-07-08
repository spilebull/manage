# Pager 書き方

```php
if ($num > 0) { // DB取得テーブルが1件以上なら以下の処理を行う
    $array_data = array();
    while ($row = f_db_fetch($rs)) { // DB取得データをShift-Jisへ変換し、配列へ
        $array_data[] = array(
            "id" => $row['id'],
            "title" => $row['title']
        );
    }
    // Pagerへのデータ割当処理
    $params = array(
        "perPage" => PER_PAGE,
        "itemData" => $array_data,
        "extraVars" => array()
    );
    $o_page = Pager::factory($params);

    foreach ($o_page->getPageData() as $item) {
        $data_list_for_page[] = $item;
    }
    $navi = $o_page->getLinks();
    echo $navi['all']; // Paging 表示
} else {
    f_show_mes("テーブルが存在しません。"); // DB取得テーブルが0件なら以下のメッセージ表示
}
```

# 画像 Upload 処理
### Upload 存在チェック `image_path` とは入力FORMの画像選択時の `name属性`
### 画像格納ファイル
```php
if ($_FILES['image_path']['name'] != "") { // 画像アップファイル名取得
    // 拡張子取得後、小文字変換
    $extension1 = strtolower(pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION));
    $filename1 = do_upload("image_path", $extension1, 1);
}
$filename_1 = ($filename1 != "") ? $filename1 . "." . $extension1 : ""; // DB保存用ファイル名生成
$posted_daytime = $posted_day . " 00:00:00"; // datetime型に合せて時間設定
```

# SQL 書き方
### 登録する値は `''` で囲む。文字列と変数の連結 `.mysql～.`
```php
$sql .= "'" . mysql_real_escape_string($code) . "',";
$sql .= "'" . mysql_real_escape_string($email) . "',";
$sql .= "'" . mysql_real_escape_string($lastname) . "',";
$sql .= "'" . mysql_real_escape_string($firstname) . "',";
$sql .= "now(),";
$sql .= "now()";
$sql .= ")";
```
