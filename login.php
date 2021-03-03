<?php 

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
if(!empty($_POST)){
    
    // 変数に代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;
    debug('POSTされた値：'.print_r($_POST,true));
    
    // バリデーション
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    
    if(empty($err_msg)){
        validEmail($email, 'email');
        validMaxLen($email, 'email');
        validMinLen($email, 'email');
        
        validPass($pass, 'pass');
        
            
            if(empty($err_msg)){
                debug('バリデーションOKです。。。。。');
                
                //例外処理
                try{
                    $dbh = dbConnect();
                    $sql = 'SELECT password,id  FROM users WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $email);
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);
                    debug('$stmtの中身だよん：'.print_r($stmt, true));
                    // クエリ結果の値を取得
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    debug('クエリ結果の中身：'.print_r($result,true));
                    
                    //パスワード照合
                    if(!empty($result) && password_verify($pass, array_shift($result))){
                        debug('パスワードが照合できました。');
                        $sesLimit = 60 * 60;
                        $_SESSION['login_date'] = time();
                        if($pass_save){
                            debug('ログイン保持にチェックがあります。');
                            $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                        }else{
                            debug('ログイン保持にチェックがありません。');
                            $_SESSION['login_limit'] = $sesLimit;
                        }
                        $_SESSION['user_id'] = $result['id'];
                        header("Location:mypage.php");
                        
                    }else{
                        debug('パスワードがアンマッチです。');
                        $err_msg['common'] = MSG09;
                    }
                    
                }catch (Exception $e){
                    error_log('エラー発生:' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
                
            }
        
    }
}
//debug('$_SESSIONの中身：'.print_r($_SESSION,true));
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php 
    $siteTitle = 'ログイン'; 
    require('head.php');
    ?>

<body class="page-login page-1colum">

    <!-- ヘッダー -->
    <header>
        <?php
            require('header.php');
            ?>
    </header>
    <p id="js-show-msg" style="display:none; position: fixed;
                               top: 0;
                               width:100%;
                               height:40px;
                               background: rgba(122,206,230,0.6);
                               text-align: center;
                               font-size:16px;
                               line-height: 40px;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
        <?php debug('セッションの中身:'.print_r($_SESSION,true)); ?>
    <!-- メインコンテンツ -->
    <div class="contents site-width">

        <!-- Main -->
        <section id="main">

            <div class="form-container">

                <form action="" class="form" method="post">
                    <h2 class="title">ログイン</h2>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                    </div>
                    <label for="" class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                        Email
                        <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                    </label>
                    パスワード
                    <label for="">
                        <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                    </label>
                    <label for="" class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                        <input type="checkbox" name="pass_save">次回からログインを省略する
                    </label>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="ログイン">
                    </div>
                    パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>
                </form>
            </div>
        </section>

    </div>

    <!-- footer -->
    <?php
        require('footer.php'); 
        ?>
