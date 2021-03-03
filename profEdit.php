<?php 
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザーデータ'.print_r($dbFormData,true)); 

//POST送信されていた場合
if(!empty($_POST)){
    debug('POST送信されています。');
    debug('POST情報：'.print_r($_POST,true));
    //debug('FILE情報：'.print_r($_FILES,true));
    
    //変数にユーザー情報を代入
    $username = $_POST['username'];
    $tel = (empty($_POST['tel'])) ? '' : $_POST['tel'];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip']: 0; //後続のバリデーションにひっかかかるため、空で送信さえてきたら０を入れる
    $addr = $_POST['addr'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    
    // DBの情報と異なる場合、バリデーションを行う
    if($dbFormData['username'] !== $username){
        //名前の最大文字数チェック
        validMaxLen($username, 'username');
    }
    
    if($dbFormData['tel'] !== $tel){
        // 電話番号の形式かチェック
        validTel($tel, 'tel');
    }
    if($zip !== (int)$dbFormData['zip']){ //DBからとってきたものは文字列になるので、数値にキャスト
        // 郵便番号の形式かチェック
        validZip($zip, 'zip');
    }
    if($dbFormData['addr'] !== $addr){
        //住所の最大文字数チェック
        validMaxLen($addr, 'addr');
    }
    if($dbFormData['age'] !== $age){
        //半角数字チェック
        validNumber($age, 'age');
    }
    if($dbFormData['email'] !== $email){
        validRequired($email,'email');
        }
    
    if(empty($err_msg)){
        debug('バリデーションOKです。');
        // 例外処理
        try{
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'UPDATE users SET username = :u_name, tel = :tel, zip = :zip, addr = :addr, age = :age, email = :email WHERE id = :u_id';
            $data = array(':u_name' => $username, ':tel' => $tel, 'zip' => $zip, ':addr' => $addr, 'age' => $age, ':email' => $email, ':u_id' => $dbFormData['id']);
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            if($stmt){
                debug('データベースの更新成功！！！！');
                $_SESSION['msg_success'] = SUC02;
                debug('マイページへ遷移します。');
                header("Location:mypage.php");
            }else{
                debug('データベースの更新に失敗しました。');
            }
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
<?php 
$siteTitle = 'プロフィール編集';
require('head.php');
?>


<body class="page-profEdit page-2colum page-logined">

    <!-- メニュー -->
    <?php 
    require('header.php');
?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        <h1 class="page-title">プロフィール編集</h1>

        <!-- Main -->
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['username'])) echo 'err' ; ?>">
                        名前
                        <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['username'])) echo $err_msg['username'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['tel'])) echo 'err' ; ?>">
                        TEL<span style="font-size:12px;">*ハイフン無しでご入力ください</span>
                        <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['tel'])) echo $err_msg['tel'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['zip'])) echo 'err' ; ?>">
                        郵便番号<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span>s
                        <input type="text" name="zip" value="<?php echo getFormData('zip'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['zip'])) echo $err_msg['zip'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['addr'])) echo 'err' ; ?>">
                        住所
                        <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['addr'])) echo $err_msg['addr'];
                        ?>
                    </div>
                    <label style="text-align:left;" class="<?php if(!empty($err_msg['age'])) echo 'err' ; ?>">
                        年齢
                        <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['age'])) echo $err_msg['age'];
                        ?>
                    </div>
                    <label for="" class="<?php if(!empty($err_msg['email'])) echo 'err' ; ?>">
                        email
                        <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="変更する">
                    </div>
                </form>
            </div>
        </section>
        
        <!-- サイドバー -->
        <?php
        require('sidebar_mypage.php');
        ?>

    </div>


    <?php
    require('footer.php');
?>
