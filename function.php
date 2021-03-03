<?php 

//================================
// ログ
//================================
ini_set('log_errors','off'); 
ini_set('error_log','php.log'); 

//================================
// デバッグ
//================================
// デバッグフラグ
$debug_flg = true;
// デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//================================
// セッション
//================================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();



//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
    }
}

//================================
// メッセージ
//================================
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）が合っていません');
define('MSG04', '半角英数字のみご利用いただけます');
define('MSG05', '六文字以上で入力してください');
define('MSG06', '255文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', '購入しました！');


//エラーメッセージ格納用の配列
$err_msg = array();


//================================
// バリデーション
//================================

function validRequired($str, $key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

function validEmailDup($email,$key){
    global $err_msg;
    try {
        // DBに接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email';
        // 流し込み
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        debug('validEmailDupの$stmtの中身：'.print_r($stmt,true));
        if(!empty(array_shift($stmt))){
            global $err_msg;
            $err_msg[$key] = MSG03;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

function validMaxLen($str, $key, $max = 256){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

function validPass($str, $key){
    validHalf($str, $key);
    validMaxLen($str, $key);
    validMinLen($str, $key);
}

function validTel($str, $key){
    if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}

function validZip($str, $key){
    if(!preg_match("/^(([0-9]{3}-[0-9]{4})|([0-9]{7}))$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG11;
    }
}

function validNumber($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG17;
    }
}

function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}

function validSelect($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG15;
    }
}
function validLength($str, $key, $len = 8){
    if( mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len. MSG14;
    }
}

//================================
// データベース
//================================
function dbConnect(){
    // DBへの接続準備
    $dsn = 'mysql:dbname=vintageshop;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // PDOオブジェクト生成
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

function queryPost($dbh, $sql, $data){
    //クエリ作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        debug('クエリに失敗しました。');
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('クエリ成功。');
    return $stmt;
}

// ユーザー情報取得
function getUser($u_id){
    debug('ユーザー情報を取得します。');
    // 例外処理
    try{
        // DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        //流し込み
        $data = array(':u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        
        //クエリ結果のデータの１レコード目を取得
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生：'. $e->getMessage());
    }
}

function getProduct($u_id, $p_id){
    debug('ユーザーID：'.$u_id);
    debug('商品ID：'.$p_id);
    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            //クエリ結果のデータを１レコード返却
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getProductList($currentMinNum = 1, $category, $sort, $span = 20){
    debug('商品情報を取得します。');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // 件数用のSQL文作成
        $sql = 'SELECT id FROM product';
        if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY price ASC';
                    break;
                case 2:
                    $sql .= ' ORDER BY price DESC';
                    break;
            }
        }
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $rst['total'] = $stmt->rowCount(); //総レコード数
        $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
        if(!$stmt){
            return false;
        }

        // ページング用のSQL文作成
        $sql = 'SELECT * FROM product';
        if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY price ASC';
                    break;
                case 2:
                    $sql .= ' ORDER BY price DESC';
                    break;
            }
        }
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL：'.$sql);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            // クエリ結果のデータを全レコードを格納
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

function getProductOne($p_id){
    debug('商品情報を取得します。');
    debug('商品ID：'.$p_id);

    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //sql文
        $sql = 'SELECT p.id, p.name, p.comment, p.price, p.pic1, p.pic2, p.pic3, p.user_id, p.create_date, p.update_date, c.name AS category
        FROM product AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND c.delete_flg = 0 AND p.delete_flg = 0';
        $data = array(':p_id' => $p_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        debug('$stmtの中身：'.print_r($stmt,true));
        //成功している場合
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}

function getCategory(){
    debug('カテゴリー情報を取得します');
    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //sql文作成
        $sql = 'SELECT * FROM category';
        $data = array();
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
            //クエリ結果の全データを返却
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
//================================
// メール送信
//================================
function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        mb_language("Japanese"); 
        mb_internal_encoding("UTF-8");
        
        // メールを送信（送信結果はtrueかfalseで帰ってくる）
        $result = mb_send_mail($to, $subject, $comment, "FROM: ".$from);
        //送信結果を判定
        if($result){
            debug('メールを送信しました。');
        }else{
            debug('【エラー発生】メールの送信に失敗しました。');
        }
    }
}


//================================
// その他
//================================
// サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

function getFormData($str){
    global $dbFormData;
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
        //フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            //POSTにデータがある場合
            if(isset($_POST[$str])){
                return sanitize($_POST[$str]);
            }else{
                //ない場合（基本ありえない）はDBの情報を表示
                return sanitize($dbFormData[$str]);
            }
        }else{
            //POSTにデータがあり、DBの情報と違う場合
            if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
                return sanitize($_POST[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    }else{
        if(isset($_POST[$str])){
            return sanitize($_POST[$str]);
        }
    }
}

//セッションを１回だけ取得する
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}
//認証キー生成
function makeRandKey($length = 8){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; ++$i){
        $str .= $chars[mt_rand(0,61)];
    }
    return $str;
}

//ページング
//$currentPageNum : 現在のページ
//$totalPageNum : 総ページ数
//$link : 検索用GETパラメータリンク
//$pageColNum : ページネーション表示数
function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
    //現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンクを４個出す
    if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
        //現在のページ数が、総ページ数の１ページ前なら、左にリンク3個、右に一個出す
    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
        //現在ページが２の場合は左にリンク一個、みぎにリンク３個出す
    }elseif( $currentPageNum = 2 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
        //現在ページが１の場合は左に何も出さない。みぎに５個
    }elseif($currentPageNum = 1 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;
        //総ページ数が表示項目より少ない場合は、総ページ数をループのMAX、るーぷのMinを１に設定
    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
        //それ以外は左に２個出す。
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }
    
    echo '<div class="pagination" style="margin-bottom:80px;">';
      echo '<ul class="pagination-list">';
        if($currentPageNum != 1){
            echo '<li class="list-item"><a href="p=1'.$link.'">&lt;</a></li>';
        }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){echo 'active';}
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
    }if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}

function uploadImg($file, $key){
    debug('画像アップロード処理開始');
    debug('FILE情報：'.print_r($file,true));

    if (isset($file['error']) && is_int($file['error'])) {
        try {
            // バリデーション
            //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
            switch ($file['error']) {
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
                    throw new RuntimeException('ファイルが選択されていません');
                    break;
                case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
                case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                    break;
                default: // その他の場合
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
                throw new RuntimeException('画像形式が未対応です');
            }

            //ファイルのパス
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            // 保存したファイルパスのパーミッション（権限）を変更する
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;

        } catch (RuntimeException $e) {

            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();

        }
    }
}

function showImg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}

//GETパラメータ付与
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){
                //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}



?>