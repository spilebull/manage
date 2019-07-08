<?php
/**
 * 共通関数定義ファイル
 *
 * @version 1.00
 * @author Tetsuhiro Koyama <te-koyama@usen.co.jp>
 * @license GNU Public License
 * @copyright Tetsuhiro Koyama
 * @package temp
 * @subpackage null
 */

// ==========================================================================
// 　関数名　：　_formstr
// 　機　能　：　配列データを一括変換
// 　　　　　：　「register_globals = Off」に対応する関数。実引数は$_POST配列か$_GET配列
// 　　　　　：　$array = _formstr($_POST);
// 　　　　　：　extract($array);
// 　　　　　：　のようにして呼び出す。
// 　　　　　：　「magic_quotes_gpc = On」のときはエスケープ解除
// 　　　　　：　自動的にhtmlタグを取り除く
// 　引　数　：　なし
// 　戻り値　：　なし
// ==========================================================================
function _formstr($array)
{
    foreach ($array as $key => $value) {

        if (! is_array($value)) {

            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $value = htmlspecialchars($value);

            $array[$key] = $value;
        }
    }
    return $array;
}

// ==========================================================================
// 　関数名　：　_unhtmlspecialchars
// 　機　能　：　文字列中のHTML特殊文字を元に戻す（htmlspecialchars() の逆）
// 　引　数　：　htmlspecialcharsで変換された文字列
// 　戻り値　：　htmlspecialchars変換を元に戻した文字列
// ==========================================================================
function _unhtmlspecialchars($pa_String)
{
    $lo_Array = array_flip(get_html_translation_table(HTML_SPECIALCHARS));
    $lo_String = strtr($pa_String, $lo_Array);
    return $lo_String;
}

// ==========================================================================
// 　関数名　：　_csvescape
// 　機　能　：　CSVファイル出力文字列をエスケープしてダブルクォーテーションでくくる。
// 　引　数　：　エスケープする文字列
// 　戻り値　：　エスケープ後の文字列
// 　補　足　：　_unhtmlspecialchars関数を一緒に定義する必要がある。
// ==========================================================================
function _csvescape($pa_String)
{
    $lo_String = _unhtmlspecialchars($pa_String);
    // ダブルクォーテーションをエスケープする（　"　→ ""　）
    $lo_String = eregi_replace("\"", "\"\"", $lo_String);
    // 文字列全体をダブルクォーテーションでくくる
    $lo_String = "\"" . $lo_String . "\"";
    return $lo_String;
}

// ==========================================================================
// 　関数名　：　_DateTimeFormat
// 　機　能　：　日付表示形式設定
// 　引　数　：　①形式設定用日付(日付時刻を表した文字列 [例]2001/1/1 15:15:15)
// 　　　　　：　②設定形式　"YYYY年m月d日"
// 　　　　　：　　　　　　　"YYYY年mm月dd日"
// 　　　　　：　　　　　　　"YYYY/m/d"
// 　　　　　：　　　　　　　"YYYY/mm/dd"
// 　　　　　：　　　　　　　"H時M分S秒"
// 　　　　　：　　　　　　　"HH時MM分SS秒"
// 　　　　　：　　　　　　　"H:M:S"
// 　　　　　：　　　　　　　"HH:MM:SS"
// 　　　　　：　　　　　　　"YYYY年m月d日 H時M分S秒"
// 　　　　　：　　　　　　　"YYYY年mm月dd日 HH時MM分SS秒"
// 　　　　　：　　　　　　　"YYYY/m/d H:M:S"
// 　　　　　：　　　　　　　"YYYY/mm/dd HH:MM:SS"
// 　　　　　：　　　　　　　"YYYYmmddHHMMSS"
// 　戻り値　：　形式設定後日付
// ==========================================================================
function _DateTimeFormat($pa_Date, $pa_Format)
{
    $lo_Date = strtotime($pa_Date);
    $lo_Year = strftime("%Y", $lo_Date);
    $lo_Month = strftime("%m", $lo_Date);
    $lo_Day = strftime("%d", $lo_Date);
    $lo_Hour = strftime("%H", $lo_Date);
    $lo_Minute = strftime("%M", $lo_Date);
    $lo_Second = strftime("%S", $lo_Date);

    if ($pa_Format == "YYYY年m月d日" || $pa_Format == "YYYY/m/d" || $pa_Format == "H時M分S秒" || $pa_Format == "H:M:S" || $pa_Format == "YYYY年m月d日 H時M分S秒" || $pa_Format == "YYYY/m/d H:M:S") {
        $lo_Month = intval($lo_Month);
        $lo_Day = intval($lo_Day);
        $lo_Hour = intval($lo_Hour);
        $lo_Minute = intval($lo_Minute);
        $lo_Second = intval($lo_Second);
    }

    if ($pa_Format == "YYYY年m月d日" || $pa_Format == "YYYY年mm月dd日") {
        $lo_Ret = $lo_Year . "年" . $lo_Month . "月" . $lo_Day . "日";
    } elseif ($pa_Format == "YYYY/m/d" || $pa_Format == "YYYY/mm/dd") {
        $lo_Ret = $lo_Year . "/" . $lo_Month . "/" . $lo_Day;
    } elseif ($pa_Format == "H時M分S秒" || $pa_Format == "HH時MM分SS秒") {
        $lo_Ret = $lo_Hour . "時" . $lo_Minute . "分" . $lo_Second . "秒";
    } elseif ($pa_Format == "H:M:S" || $pa_Format == "HH:MM:SS") {
        $lo_Ret = $lo_Hour . ":" . $lo_Minute . ":" . $lo_Second;
    } elseif ($pa_Format == "YYYY年m月d日 H時M分S秒" || $pa_Format == "YYYY年mm月dd日 HH時MM分SS秒") {
        $lo_Ret = $lo_Year . "年" . $lo_Month . "月" . $lo_Day . "日" . " ";
        $lo_Ret .= $lo_Hour . "時" . $lo_Minute . "分" . $lo_Second . "秒";
    } elseif ($pa_Format == "YYYY/m/d H:M:S" || $pa_Format == "YYYY/mm/dd HH:MM:SS") {
        $lo_Ret = $lo_Year . "/" . $lo_Month . "/" . $lo_Day . " ";
        $lo_Ret .= $lo_Hour . ":" . $lo_Minute . ":" . $lo_Second;
    } elseif ($pa_Format == "YYYYmmddHHMMSS") {
        $lo_Ret = $lo_Year . $lo_Month . $lo_Day . $lo_Hour . $lo_Minute . $lo_Second;
    } else {
        $lo_Ret = "形式の設定が不正です。";
    }
    return $lo_Ret;
}

// ==========================================================================
// 　関数名　：　_DateAdd
// 　機　能　：　第一パラメータで渡された日付に第二パラメータの日付分加算した
// 　　　　　：　日付を返却する。
// 　引　数　：　①日付文字列　　[例]2001/1/1
// 　　　　　：　②加算日数　　　[例]3
// 　戻り値　：　加算された日付　[例]2004/1/4
// ==========================================================================
function _DateAdd($pa_Date, $pa_Add)
{
    $lo_OneDayTime = 86400; // 1日の秒数

    $lo_Date = strtotime($pa_Date);

    $lo_Timestamp = $lo_Date + ($lo_OneDayTime * $pa_Add);
    $lo_RetDate = date("Y/m/d", $lo_Timestamp);

    return $lo_RetDate;
}

// ==========================================================================
// 　関数名　：　_chkEmail
// 　機　能　：　メールアドレスの形式が正しいかチェックする。
// 　引　数　：　メールアドレス
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkEmail($string)
{
    $lo_Ret = ereg("^[!-?A-~]+@[!-?A-~]+\.[!-?A-~]+$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

// ==========================================================================
// 　関数名　：　_chkEmailMulti
// 　機　能　：　メールアドレスの形式が正しいかチェックする。
// 　　　　　：　※複数のメールアドレス入力時のチェックである。
// 　　　　　：　　メールアドレスは , で区切られているものとする。
// 　引　数　：　メールアドレス
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkEmailMulti($string)
{
    $lo_Ret = ereg("^([!-?A-~]+@[!-?A-~]+\.[!-?A-~]+,*)+$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

// ==========================================================================
// 　関数名　：　_chkKazu
// 　機　能　：　半角数字のみかチェックする。
// 　引　数　：　文字列
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkKazu($string)
{
    $lo_Ret = ereg("^[0-9]+$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

// ==========================================================================
// 　関数名　：　_chkHankakuEisuKazu
// 　機　能　：　半角英数字のみかチェックする。
// 　引　数　：　文字列
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkHankakuEisuKazu($string)
{
    $lo_Ret = ereg("^[a-zA-Z0-9]+$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}
// ==========================================================================
// 　関数名　：　_chkNum
// 　機　能　：　半角数字と「-」のみかチェックする。
// 　引　数　：　文字列
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkNum($string)
{
    $lo_Ret = ereg("^[0-9\-]+$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}
// ==========================================================================
// 　関数名　：　_chkZip
// 　機　能　：　000-0000形式かチェックする
// 　引　数　：　文字列
// 　戻り値　：　形式が正しい時 = TRUE
// 　　　　　：　形式が正しくない時 = FALSE
// ==========================================================================
function _chkZip($string)
{
    $lo_Ret = ereg("^[0-9]{3}-[0-9]{4}$", $string);
    if ($lo_Ret == TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}
// ==========================================================================
// 　関数名　：　_GetNow
// 　機　能　：　現在日時を取得
// 　引　数　：　なし
// 　戻り値　：　現在日時
// ==========================================================================
function _GetNow()
{
    $lo_Now = date("Y/m/d H:i:s", time());
    return $lo_Now;
}

// ==========================================================================
// 　関数名　：　_DateInterval
// 　機　能　：　パラメータで渡された２つ日付の差（何日）を取得する。
// 　引　数　：　①日付文字列 [例]2001/1/1
// 　　　　　：　②日付文字列 [例]2001/1/2
// 　戻り値　：　パラメータで渡された２つ日付の差（何日）
// ==========================================================================
function _DateInterval($pa_Date1, $pa_Date2)
{
    $lo_OneDayTime = 86400; // 1日の秒数

    $lo_Date1 = strtotime($pa_Date1);
    $lo_Date2 = strtotime($pa_Date2);

    $lo_IntervalTimestamp = $lo_Date2 - $lo_Date1;
    $lo_DateInterval = $lo_IntervalTimestamp / $lo_OneDayTime;

    return $lo_DateInterval;
}

// ==========================================================================
// 　関数名　：　_GetPassword
// 　機　能　：　パスワード生成して返却する。
// 　引　数　：　①生成されたパスワード・・・・・・・・・・・返却値パラメータ
// 　　　　　：　②生成されたパスワードのフリガナ・・・・・・返却値パラメータ
// 　　　　　：　③生成するパスワードの桁数（省略可能）
// 　戻り値　：　パラメータで渡された２つ日付の差（何日）
// ==========================================================================
function _GetPassword(&$pa_Password, &$pa_PasswordFuri, $pa_Ketasu = 8)
{
    $lo_PassStr = array(
        "a" => "エー",
        "b" => "ビー",
        "c" => "シー",
        "d" => "ディー",
        "e" => "イー",
        "f" => "エフ",
        "g" => "ジー",
        "h" => "エイチ",
        "i" => "アイ",
        "j" => "ジェイ",
        "k" => "ケイ",
        "l" => "エル",
        "m" => "エム",
        "n" => "エヌ",
        "o" => "オー",
        "p" => "ピー",
        "q" => "キュー",
        "r" => "アール",
        "s" => "エス",
        "t" => "ティ",
        "u" => "ユー",
        "v" => "ブイ",
        "w" => "ダブリュ",
        "x" => "エックス",
        "y" => "ワイ",
        "z" => "ゼット",
        "A" => "エー",
        "B" => "ビー",
        "C" => "シー",
        "D" => "ディー",
        "E" => "イー",
        "F" => "エフ",
        "G" => "ジー",
        "H" => "エイチ",
        "I" => "アイ",
        "J" => "ジェイ",
        "K" => "ケイ",
        "L" => "エル",
        "M" => "エム",
        "N" => "エヌ",
        "O" => "オー",
        "P" => "ピー",
        "Q" => "キュー",
        "R" => "アール",
        "S" => "エス",
        "T" => "ティ",
        "U" => "ユー",
        "V" => "ブイ",
        "W" => "ダブリュ",
        "X" => "エックス",
        "Y" => "ワイ",
        "Z" => "ゼット",
        "1" => "ワン",
        "2" => "ツー",
        "3" => "スリー",
        "4" => "フォー",
        "5" => "ファイブ",
        "6" => "シックス",
        "7" => "セブン",
        "8" => "エイト",
        "9" => "ナイン",
        "0" => "ゼロ"
    );

    $lo_Password = "";
    // 英数混合のパスワードが作成されるまで繰り返す。
    while (ereg("[0-9]", $lo_Password) == FALSE || ereg("[a-zA-Z]", $lo_Password) == FALSE) {
        $i = 1;
        $lo_RandNumOld = "";
        $lo_Password = "";
        $lo_PasswordFuri = "";
        srand((float) microtime() * 10000000);
        while ($i <= $pa_Ketasu) {
            $lo_RandNum = array_rand($lo_PassStr);
            // 同じ文字が２つ以上連続している時は再取得する。
            if ($lo_RandNum != $lo_RandNumOld) {
                $lo_RandNumOld = $lo_RandNum;
                $lo_Password .= $lo_RandNum;
                if ($i > 1) {
                    $lo_PasswordFuri .= "・";
                }
                $lo_PasswordFuri .= $lo_PassStr[$lo_RandNum];
                $i ++;
            }
        }
    }

    // 返却値用パラメータを設定
    $pa_Password = $lo_Password;
    $pa_PasswordFuri = $lo_PasswordFuri;
}

// ==========================================================================
// 　関数名　：　_getCookie
// 　機　能　：　クッキーを取得する。
// 　引　数　：　クッキー名
// 　戻り値　：　クッキーに保存されている値
// ==========================================================================
function _getCookie($pa_CookieName)
{
    $lo_CookieVars = $_COOKIE[$pa_CookieName];
    return $lo_CookieVars;
}

// ==========================================================================
// 　関数名　：　_setCookie
// 　機　能　：　クッキーを設定する。
// 　引　数　：　①クッキー名
// 　　　　　：　②クッキーの値
// 　　　　　：　③有効期限　単位：秒(現在から何秒後かを指定　[例]3600 ・・・ 1時間後)
// 　　　　　：　　※ "" がパラメータで渡された時は「1970/1/1 00:00」に設定される。
// 　戻り値　：　クッキーの送信に成功 = TRUE
// 　　　　　：　クッキーの送信に失敗 = FALSE
// ==========================================================================
function _setCookie($pa_CookieName, $pa_CookieVars, $pa_Validity)
{
    if ($pa_Validity != "" && is_numeric($pa_Validity) == TRUE) {
        $lo_Ret = setcookie($pa_CookieName, $pa_CookieVars, time() + $pa_Validity);
    } else {
        $lo_Ret = setcookie($pa_CookieName, $pa_CookieVars);
    }
    return $lo_Ret;
}

// ==========================================================================
// 　関数名　：　_getAgent
// 　機　能　：接続中の携帯端末を示す番号を返す
// 　引　数　：なし
// 　戻り値　：0 = 以下に該当しない未定義の端末またはPC
// 　　　　　：1 = i-mode
// 　　　　　：2 = Foma
// 　　　　　：3 = j-sky非パケット
// 　　　　　：4 = Vodafoneパケット
// 　　　　　：5 = Vodafone(3G)
// 　　　　　：6 = Ezweb HDML対応
// 　　　　　：7 = Ezweb HTML対応
// ==========================================================================
function _getAgent()
{
    if (preg_match("/^DoCoMo\/1/", $_SERVER['HTTP_USER_AGENT']))
        return 1;
    elseif (preg_match("/^DoCoMo\/2/", $_SERVER['HTTP_USER_AGENT']))
        return 2;
    elseif (preg_match("/^J-PHONE\/[23]/", $_SERVER['HTTP_USER_AGENT']))
        return 3;
    elseif (preg_match("/^J-PHONE\/[456]/", $_SERVER['HTTP_USER_AGENT']))
        return 4;
    elseif (preg_match("/^Vodafone/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/^MOT\-/", $_SERVER['HTTP_USER_AGENT']))
        return 5;
    elseif (preg_match("/^UP\.Browser/", $_SERVER['HTTP_USER_AGENT']))
        return 6;
    elseif (preg_match("/^KDDI/", $_SERVER['HTTP_USER_AGENT']))
        return 7;
    else
        return 0;
}

// ==========================================================================
// 　関数名　：　_SetSelectYear
// 　機　能　：　年ドロップダウンリストを設定する。
// 　引　数　：　$pa_start : 表示開始年
// 　　　　　：　$pa_nensu : 表示する年数(何年間分表示するか)
// 　　　　　：　$pa_keydefault : 初期選択値キー(省略したい時は "" を指定する。)
// 　戻り値　：　なし
// ==========================================================================
function _SetSelectYear($pa_start, $pa_nensu, $pa_keydefault)
{
    for ($i = 0; $i < $pa_nensu; $i ++) {
        $lo_Work = $pa_start + $i;
        print "<option value='" . $lo_Work . "'";
        if ($lo_Work == $pa_keydefault) {
            print " selected";
        }
        print ">" . $lo_Work . "</option>";
    }
}

// ==========================================================================
// 　関数名　：　_SetSelectMonth
// 　機　能　：　月ドロップダウンリストを設定する。
// 　引　数　：　$pa_keydefault : 初期選択値キー(省略したい時は "" を指定する。)
// 　戻り値　：　なし
// ==========================================================================
function _SetSelectMonth($pa_keydefault)
{
    for ($i = 1; $i <= 12; $i ++) {
        print "<option value='" . $i . "'";
        if ($i == $pa_keydefault) {
            print " selected";
        }
        print ">" . $i . "</option>";
    }
}

// ==========================================================================
// 　関数名　：　_SetSelectDay
// 　機　能　：　日ドロップダウンリストを設定する。
// 　引　数　：　$pa_keydefault : 初期選択値キー(省略したい時は "" を指定する。)
// 　戻り値　：　なし
// ==========================================================================
function _SetSelectDay($pa_keydefault)
{
    for ($i = 1; $i <= 31; $i ++) {
        print "<option value='" . $i . "'";
        if ($i == $pa_keydefault) {
            print " selected";
        }
        print ">" . $i . "</option>";
    }
}

// ==========================================================================
// 　関数名　：　_SetSelectHour
// 　機　能　：　時間ドロップダウンリストを設定する。
// 　引　数　：　$pa_keydefault : 初期選択値キー(省略したい時は "" を指定する。)
// 　戻り値　：　なし
// ==========================================================================
function _SetSelectHour($pa_keydefault)
{
    for ($i = 0; $i <= 23; $i ++) {
        print "<option value='" . $i . "'";
        if ($i == $pa_keydefault) {
            print " selected";
        }
        print ">" . $i . "</option>";
    }
}

// ==========================================================================
// 　関数名　：　_SetSelectMinute
// 　機　能　：　分ドロップダウンリストを設定する。
// 　引　数　：　$pa_keydefault : 初期選択値キー(省略したい時は "" を指定する。)
// 　戻り値　：　なし
// ==========================================================================
function _SetSelectMinute($pa_keydefault)
{
    for ($i = 0; $i <= 59; $i ++) {
        $lo_Work = substr("00" . $i, strlen("00" . $i) - 2, 2);
        print "<option value='" . $lo_Work . "'";
        if ($lo_Work == $pa_keydefault) {
            print " selected";
        }
        print ">" . $lo_Work . "</option>";
    }
}

// ==========================================================================
// 　関数名　：　_checkdate
// 　機　能　：　DBから指定条件に一致するレコード数を取得する。
// 　引　数　：　$pa_m ： 月
// 　　　　　：　$pa_d ： 日
// 　　　　　：　$pa_y ： 年
// 　戻り値　：　TRUE ：　チェックＯＫ（日付として正しい）
// 　　　　　：　FALSE ：　チェックＮＧ（日付として不正）
// ==========================================================================
function _checkdate($pa_m, $pa_d, $pa_y)
{
    if (is_numeric($pa_m) != TRUE || is_numeric($pa_d) != TRUE || is_numeric($pa_y) != TRUE) {
        return FALSE;
    }
    $lo_Ret = checkdate($pa_m, $pa_d, $pa_y);
    return $lo_Ret;
}

// ==========================================================================
// 　関数名　：　_encs
// 　機　能　：　変換前の文字コードに関わらずEUCからShift-Jisへ変換する。
// 　引　数　：　変換前の文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _encs($pa_Str)
{
    return mb_convert_encoding($pa_Str, "SJIS", "EUC-JP");
}

// ==========================================================================
// 　関数名　：　_encu
// 　機　能　：　変換前の文字コードに関わらずShift-JisからUTF-8へ変換する。
// 　引　数　：　変換前の文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _encu($pa_Str)
{
    return mb_convert_encoding($pa_Str, "UTF-8", "SJIS");
}

// ==========================================================================
// 　関数名　：　_encsArray
// 　機　能　：　変換前の文字コードに関わらずEUCからShift-Jisへ変換する。
// 　　　　　：　配列に格納されている値をすべて変換する。
// 　引　数　：　変換前の文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _encsArray($array)
{
    foreach ($array as $key => $value) {
        $array[$key] = _encs($value);
    }
    return $array;
}

// ==========================================================================
// 　関数名　：　_ence
// 　機　能　：　変換前の文字コードに関わらずShift-JisからEUCへ変換する。
// 　引　数　：　変換前の文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _ence($pa_Str)
{
    return mb_convert_encoding($pa_Str, "EUC-JP", "SJIS");
}

// ==========================================================================
// 　関数名　：　_enceArray
// 　機　能　：　変換前の文字コードに関わらずShift-JisからEUCへ変換する。
// 　　　　　：　配列に格納されている値をすべて変換する。
// 　引　数　：　変換前の文字列
// 　戻り値　：　変換後の文字列
// ==========================================================================
function _enceArray($array)
{
    foreach ($array as $key => $value) {
        $array[$key] = _ence($value);
    }
    return $array;
}

// ==========================================================================
// 　関数名　：　_fnetmail
// 　機　能　：　メールを送信する。
// 　引　数　：　なし
// 　戻り値　：　なし
// ==========================================================================
function _fnetmail($mailto, $mailfrom, $mailsubject, $mailbody, $mailcc, $mailbcc)
{

    // 文字コードを変換する(件名)
    $det_enc = mb_detect_encoding($mailsubject, "EUC-JP, SJIS, JIS");
    if ($det_enc and $det_enc != "JIS") {
        $mailsubject = '=?ISO-2022-JP?B?' . base64_encode(mb_convert_encoding($mailsubject, "JIS", $det_enc)) . '?=';
    } else {
        $mailsubject = '=?ISO-2022-JP?B?' . base64_encode($mailsubject) . '?=';
    }

    // 文字コードを変換する(本文)
    $det_enc = mb_detect_encoding($mailbody, "EUC-JP, SJIS, JIS");
    if ($det_enc and $det_enc != "JIS") {
        $mailbody = mb_convert_encoding($mailbody, "JIS", $det_enc);
    }

    // 追加ヘッダ
    $headers = "";
    if ($mailfrom != "") {
        $headers .= "From: " . $mailfrom . "\r\n";
    }
    if ($mailcc != "") {
        $headers .= "Cc: " . $mailcc . "\r\n";
    }
    if ($mailbcc != "") {
        $headers .= "Bcc: " . $mailbcc . "\r\n";
    }

    $headers .= "Content-Type: text/plain; charset=ISO-2022-JP\r\n";
    $headers .= "Content-Transfer-Encoding: 7bit\r\n";

    /* ここでメールを送信する */
    mail($mailto, $mailsubject, $mailbody, $headers) or die("メールが送信できませんでした");

    return TRUE;
}

// ==========================================================================
// 　関数名　：　_fnetmail_sendurl
// 　機　能　：　メールを送信する。
// 　　　　　：　送信エラーが発生しても強制終了(die)しない。
// 　引　数　：　なし
// 　戻り値　：　TRUE:送信成功、FALSE:送信エラー
// ==========================================================================
function _fnetmail_sendurl($mailto, $mailfrom, $mailsubject, $mailbody, $mailcc)
{

    // 文字コードを変換する(件名)
    $det_enc = mb_detect_encoding($mailsubject, "EUC-JP, SJIS, JIS");
    if ($det_enc and $det_enc != "JIS") {
        $mailsubject = '=?ISO-2022-JP?B?' . base64_encode(mb_convert_encoding($mailsubject, "JIS", $det_enc)) . '?=';
    } else {
        $mailsubject = '=?ISO-2022-JP?B?' . base64_encode($mailsubject) . '?=';
    }

    // 文字コードを変換する(本文)
    $det_enc = mb_detect_encoding($mailbody, "EUC-JP, SJIS, JIS");
    if ($det_enc and $det_enc != "JIS") {
        $mailbody = mb_convert_encoding($mailbody, "JIS", $det_enc);
    }

    // 追加ヘッダ
    $headers = "";
    if ($mailfrom != "") {
        $headers .= "From: " . $mailfrom . "\r\n";
    }
    if ($mailcc != "") {
        $headers .= "Cc: " . $mailcc . "\r\n";
    }

    $headers .= "Content-Type: text/plain; charset=ISO-2022-JP\r\n";
    $headers .= "Content-Transfer-Encoding: 7bit\r\n";

    /* ここでメールを送信する */
    $lo_Ret = mail($mailto, $mailsubject, $mailbody, $headers);

    return $lo_Ret;
}

// ==========================================================================
// 　関数名　：　_getdiv_htmltempimg
// 　機　能　：　画像圧縮サイズ計算
// 　引　数　：　①画像サイズ
// 　　　　　：　②圧縮後の面積
// 　戻り値　：　なし
// ==========================================================================
function _getdiv_htmltempimg($size, $menseki)
{
    if ($size[0] == 0) {
        return (0);
    }
    $div = sqrt($size[0] * $size[1] / $menseki);
    return ($div);
}

// ==========================================================================
// 　関数名　：　_hidden
// 　機　能　：　<input type="hidden" name="キー" value="バリュー">を作成する。
// 　引　数　：　配列<name="キー" value="バリュー">
// 　戻り値　：　なし
// ==========================================================================
function _hidden($array)
{
    foreach ($array as $key => $value) {
        print "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\">\n";
    }
}

// ==========================================================================
// 　関数名　：　g_makefilepath
// 　機　能　：　ファイル名の生成
// 　引　数　：　$dir　ファイル生成ディレクトリ
// 　引　数　：　$ext　拡張子
// 　引　数　：　$add_str　ファイル名付属文字
// 　戻り値　：　TRUE:正常 FALSE:異常
// 　コメント：　ファイル名は「タイムスタンプ.拡張子」になる
// ==========================================================================
function g_makefilepath($dir, $ext)
{

    // 乱数の初期化
    list ($msec, $sec) = split(" ", microtime());
    mt_srand($msec * 100000);

    $fname = date("YmdHis");
    $path = $dir . $fname . "." . $ext;

    for (;;) {
        if (file_exists($path) == FALSE) {
            break;
        }
        // 生成したファイル名が既に存在する場合は乱数を付加(重複を許さない)
        $fname = $fname . mt_rand(0, 9);
        $path = $dir . $fname . "." . $ext;
    }
    return ($path);
}
// ==========================================================================
// 　関数名　：　g_getext
// 　機　能　：　拡張子の取得
// 　引　数　：　$path　パス
// 　戻り値　：　拡張子
// ==========================================================================
function g_getext($path)
{
    $arypath = split("/", $path); // アップされたファイルが一時保存されているファイル名を/で分割して配列に格納
    $aryfile = split("\.", $arypath[count($arypath) - 1]); // 配列の回数から-1（配列の一番最後）の分（ファイル名が入ってる配列）.で分割し配列に格納

    $ext = $aryfile[count($aryfile) - 1];

    $up_name = explode('.', $path);
    list ($f_name, $ext) = $up_name;

    return ($ext);
}

// ==========================================================================
// 　関数名　：　g_movefile_unlink
// 　機　能　：　ファイルの移動（移動後、移動元ファイルを削除）
// 　引　数　：　$src　移動元
// 　引　数　：　$dst　移動先
// 　戻り値　：　TRUE:正常 FALSE:異常
// ==========================================================================
function g_movefile_unlink($src, $dst)
{
    if (file_exists($src)) {
        if (copy($src, $dst) == FALSE) {
            return (FALSE);
        }
        if (unlink($src) == FALSE) {
            return (FALSE);
        }
    } else {
        return (FALSE);
    }

    return (TRUE);
}
// ==============================================
// 関数名 : _create_img
// 機 能 : 画像リサイズ処理
// 引 数 : $img:画像ファイル（ディレクトリまで指定）$syurui：switch文case参照
// 戻り値 : なし
// ==============================================
function _create_img($img, $syurui)
{
    $imsize = getimagesize($img); // 画像の大きさを取得

    // リサイズ設定
    switch ($syurui) {

        case "0": // トピックス小画像
            $basicsize[0] = 200;
            $basicsize[1] = 150;
            break;
    }

    if ($imsize[0] <= $basicsize[0] && $imsize[1] <= $basicsize[1]) {
        return;
    }

    if ($imsize[0] >= $basicsize[0] || $imsize[1] >= $basicsize[1]) {

        if ($imsize[0] > $imsize[1]) {
            $newwidth = $basicsize[0]; // 横幅を指定
            $newheight = $newwidth * $imsize[1] / $imsize[0]; // 縦幅を指定
        } else {
            $newheight = $basicsize[1]; // 縦幅を指定
            $newwidth = $newheight / $imsize[1] * $imsize[0]; // 横幅を指定
        }
    } else {
        $newwidth = $imsize[0];
        $newheight = $imsize[1];
    }

    // サイズ変換コマンドの作成
    $w = "/usr/bin/convert -geometry  " . $newwidth . "x" . $newheight . "\! " . $img . " " . $img;
    // ↑ ↑
    // 　読込ファイル名 書出ファイル名
    // コマンドの実行
    exec($w);
}
// ==========================================================================
// 　関数名　：　g_clear_overlimitfile
// 　機　能　：　指定期間を経過したファイルを削除する
// 　引　数　：　$path 対象ディレクトリ
// 　引　数　：　$time 経過時間(単位:時間)
// 　戻り値　：　TRUE:正常 FALSE:異常
// ==========================================================================
function g_clear_overlimitfile($path, $time)
{
    if (! ($dir = opendir($path)))
        return (FALSE);

        // 期限時刻を算出
    $limit = time() - ($time * 60 * 60);
    // 期限を過ぎたファイルを削除
    while ($fnm = readdir($dir)) {
        if (is_dir($fnm))
            continue;
        if (filectime($path . $fnm) < $limit) {
            if (unlink($path . $fnm) == FALSE)
                return (FALSE);
        }
    }
    closedir($dir);
    return (TRUE);
}
// ページング
class TekitouPager
{

    private $dataArr = array();

    private $pageNum = null;

    private $maxPageNum = null;

    function __construct($allDataArr, $maxRecodeNum, $pageNum = '')
    {
        // ページ番号
        $this->pageNum = intval($pageNum);
        // 配列を分割
        $data = array_chunk($allDataArr, $maxRecodeNum);
        // 最大ページ数
        $this->maxPageNum = count($data);
        // ページデータ
        $this->dataArr = $data[$this->pageNum];
    }

    function getPageData()
    {
        return $this->dataArr;
    }

    function getNaviLink($hardid = null)
    {
        $naviLink = null;
        for ($i = 0; $i < $this->maxPageNum; $i ++) {
            // 現在のページと一致する場合、太字等の強調
            if ($i != $this->pageNum) {
                if ($hardid == null) {
                    $naviLink .= sprintf('<a href="%s?page=%d">%d</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $i, $i + 1);
                } else {
                    $naviLink .= sprintf('<a href="%s?page=%d&hard_id=%d">%d</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $i, $hardid, $i + 1);
                }
            } else {
                $naviLink .= sprintf('<b>%d</b>&nbsp;', $i + 1);
                /*
                 * if($hardid == null){
                 * $naviLink .= sprintf('<b><a href="%s?page=%d">%d</a></b>&nbsp;', $_SERVER['SCRIPT_NAME'], $i, $i+1);
                 * }else{
                 * $naviLink .= sprintf('<b><a href="%s?page=%d&hard_id=%d">%d</a></b>&nbsp;', $_SERVER['SCRIPT_NAME'], $i,$hardid, $i+1);
                 * }
                 */
            }
        }
        return $naviLink;
    }

    function getNextLink($hardid = null)
    {
        // 次のページが存在する場合、次のページへのリンク
        if ($this->pageNum < $this->maxPageNum - 1) {
            if ($hardid == null) {
                return sprintf('<a href="%s?page=%d">次へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum + 1);
            } else {
                return sprintf('<a href="%s?page=%d&hard_id=%d">次へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum + 1, $hardid);
            }
        }
    }

    function getPrevLink($hardid = null)
    {
        // 前のページが存在する場合、前のページへのリンク
        if (0 < $this->pageNum) {
            if ($hardid == null) {
                return sprintf('<a href="%s?page=%d">前へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum - 1);
            } else {
                return sprintf('<a href="%s?page=%d&hard_id=%d">前へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum - 1, $hardid);
            }
        }
    }
}

// ページング
class TekitouPager2
{

    private $dataArr = array();

    private $pageNum = null;

    private $maxPageNum = null;

    function __construct($allDataArr, $maxRecodeNum, $pageNum = '')
    {
        // ページ番号
        $this->pageNum = intval($pageNum);
        // 配列を分割
        $data = array_chunk($allDataArr, $maxRecodeNum);
        // 最大ページ数
        $this->maxPageNum = count($data);
        // ページデータ
        $this->dataArr = $data[$this->pageNum];
    }

    function getPageData()
    {
        return $this->dataArr;
    }

    function getNaviLink($serch_parent, $serch_child, $serch_com)
    {
        $naviLink = null;
        for ($i = 0; $i < $this->maxPageNum; $i ++) {
            // 現在のページと一致する場合、太字等の強調
            if ($i != $this->pageNum) {
                $naviLink .= sprintf('<a href="%s?page=%d&serch_parent=%d&serch_child=%d&serch_com=%d">%d</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $i, $serch_parent, $serch_child, $serch_com, $i + 1);
            } else {
                $naviLink .= sprintf('<b>%d</b>&nbsp;', $i + 1);
            }
        }
        return $naviLink;
    }

    function getNextLink($serch_parent, $serch_child, $serch_com)
    {
        // 次のページが存在する場合、次のページへのリンク
        if ($this->pageNum < $this->maxPageNum - 1) {
            return sprintf('<a href="%s?page=%d&serch_parent=%d&serch_child=%d&serch_com=%d">次へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum + 1, $serch_parent, $serch_child, $serch_com);
        }
    }

    function getPrevLink($serch_parent, $serch_child, $serch_com)
    {
        // 前のページが存在する場合、前のページへのリンク
        if (0 < $this->pageNum) {
            return sprintf('<a href="%s?page=%d&serch_parent=%d&serch_child=%d&serch_com=%d">前へ</a>&nbsp;', $_SERVER['SCRIPT_NAME'], $this->pageNum - 1, $serch_parent, $serch_child, $serch_com);
        }
    }
}

// ------------------------------------------------//
// 時間を作成
// ------------------------------------------------//
function sethours($defaulttime = null)
{
    for ($i = 0; $i <= 24; $i ++) {
        $c_pad = sprintf("%02d", $i);

        if ($defaulttime != null && $defaulttime == $c_pad) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $c_pad . "'" . $selected . ">" . $c_pad . "</option>\n";
    }
}

// ------------------------------------------------//
// 分を作成
// ------------------------------------------------//
function setminute($defaulttime = null)
{
    for ($i = 0; $i <= 59; $i ++) {
        $c_pad = sprintf("%02d", $i);

        if ($defaulttime != null && $defaulttime == $c_pad) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $c_pad . "'" . $selected . ">" . $c_pad . "</option>\n";
    }
}

// ------------------------------------------------//
// 秒を作成
// ------------------------------------------------//
function setsecond($defaulttime = null)
{
    for ($i = 0; $i <= 59; $i ++) {
        $c_pad = sprintf("%02d", $i);

        if ($defaulttime != null && $defaulttime == $c_pad) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $c_pad . "'" . $selected . ">" . $c_pad . "</option>\n";
    }
}
// ------------------------------------------------//
// 年の自動生成
// ------------------------------------------------//
function get_year($start, $end, $default = null)
{
    for ($i = $start; $i <= $end; $i ++) {
        if ($default != null && $i == $default) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $i . "'>" . $i . "年</option>\n";
    }
}
// ------------------------------------------------//
// 月の自動生成
// ------------------------------------------------//
function get_month($default = null)
{
    for ($i = 1; $i <= 12; $i ++) {
        if ($default != null && $i == $default) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $i . "'>" . $i . "月</option>\n";
    }
}

// ------------------------------------------------//
// 日の自動生成
// ------------------------------------------------//
function get_day($default = null)
{
    for ($i = 1; $i <= 31; $i ++) {
        if ($default != null && $i == $default) {
            $selected = " selected ";
        } else {
            $selected = "";
        }
        echo "<option value='" . $i . "'>" . $i . "日</option>\n";
    }
}
?>
