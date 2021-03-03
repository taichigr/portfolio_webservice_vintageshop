<?php 

require('function.php');

//require('auth.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


// 処理　postされていた場合
if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    
    // 未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');
    
    if(empty($err_msg)){
        // Emailのバリデーション
        // emailの形式
        validEmail($email, 'email');
        // 最大
        validMaxLen($email, 'email');
        // 最小
        validMinLen($email, 'email');
        
        //パスワードのバリデーション
        // 半角か
        validHalf($pass, 'pass');
        // 最大
        validMaxLen($pass, 'pass');
        //最小
        validMinLen($pass, 'pass');
        
        // パスワード再入力のバリデーション
        validMaxLen($pass_re, 'pass_re');
        validMinLen($pass_re, 'pass_re');
        
        debug('全てのバリデーション完了');
        
        if(empty($err_msg)){
            // パスワードとパスワード再入力が一致しているか
            validMatch($pass, $pass_re, 'pass');
            
            if(empty($err_msg)){
                // 例外処理
                try{
                    // DB接続
                    $dbh = dbConnect();
                    // sql文作成
                    $sql = 'INSERT INTO users (email, password, login_time, create_date) VALUES (:email, :pass, :login_time, :create_date)';
                    $data = array(
                        ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s')
                    );
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);
                    debug('$stmtの中身:'.print_r($stmt,true));
                    // クエリ成功の場合
                    if($stmt){
                        debug('インサートは成功です。');
                        // ログイン状態のためのセッションを入れる、まずはログイン有効期限
                        $sesLimit = 60*60;
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        
                        header("Location:mypage.php");
                    }
                } catch (Exception $e) {
                    error_log('エラー発生：'.$e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}

debug('<<<<<<<<<<<<<<<画面表示終了<<<<<<<<<<<<<');

?>


    <?php 
    $siteTitle = 'ユーザー登録'; 
    require('head.php');
    ?>
    
    <body class="page-login page-1colum">

        <!-- ヘッダー -->
        <?php 
        require('header.php');
         ?>
        <!-- メインコンテンツ -->
        <div class="contents site-width">

            <!-- Main -->
            <section id="main">
                <div class="form-container">
                    
                    <form action="" method="post" class="form">
                        <h2 class="title">ユーザー情報</h2>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                            Email
                            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                        </label>
                        <div class="area-msg">
                            <?php echo getErrMsg('email'); ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                            パスワード<span style="font-size:12px;">＊英数字6文字以上</span>
                            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                        </label>
                        <div class="area-msg">
                            <?php echo getErrMsg('pass'); ?>
                        </div>
                        <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                            パスワード再入力
                            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
                        </label>
                        <div class="area-msg">
                            <?php echo getErrMsg('pass_re'); ?>
                        </div>
                        <div class="btn-container">
                            <input type="submit" class="btn btn-mid" value="登録する">
                        </div>
                    </form>
                </div>
            </section>

        </div>

        <!-- footer -->
        <?php
        require('footer.php'); 
        ?>
