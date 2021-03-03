<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行認証キー入力ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証はなし（ログインできない人が使う画面なので）

//セッションに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
    header("Location:passRemindSend.php"); //認証キー送信ページへ
    exit;
}

//================================
// 画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
    debug('ポスト送信されています。');
    debug('POST情報：'.print_r($_POST,true));
    
    //変数に認証キー代入 この画面で入力された認証キーをtokenとする
    $auth_key = $_POST['token'];
    
    //未入力チェック
    validRequired($auth_key,'token');
    
    if(empty($err_msg)){
        //ながさ、半角チェック
        validLength($auth_key,'token');
        validHalf($auth_key,'token');
        
        if(empty($err_msg)){
            debug('バリデーションOK。');
            
            if($auth_key !== $_SESSION['auth_key']){
                $err_msg['common'] = MSG15;
            }
            if(time()>$_SESSION['auth_key_limit']){
                $err_msg['common'] = MSG16;
            }
            
            if(empty($err_msg)){
                debug('認証OKです。');
                // パスワード生成
                $pass = makeRandKey();
                debug('生成したパスワード:'.$pass); //本来はログに出さない
                
                //例外処理
                try{
                    $dbh = dbConnect();
                    //SQL文作成　パスワードをアップデート
                    $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
                    //クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);
                    
                    //クエリ成功の場合
                    if($stmt){
                        debug('クエリ成功。アップデート成功');
                        
                        //メール送信
                        $from = 'taichi@gmail.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行完了】｜vintage shop';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://localhost:8888/vintageshop_webservice/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////

URL  
E-mail 
////////////////////////////////////////
EOT;
                        sendMail($from, $to, $subject, $comment);
                        
                        //セッション削除
                        session_unset();
                        $_SESSION['msg_success'] = SUC03;
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        
                        header("Location:login.php");
                        return;
                    }else{
                        debug('クエリに失敗しました。');
                        $err_msg['common'] = MSG07;
                    }
                }catch (Exception $e){
                    error_log('エラー発生:' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}

?>


<?php 
$siteTitle = 'パスワード再発行　認証';
require('head.php');
?>


<body class="page-passRemindReceive page-1colum">


    <!-- メニュー -->
    <?php 
    require('header.php');
    ?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); //getSessionFlashによって$_SESSION['msg_success']の中身が削除される?>
    </p>

    <!-- メインコンテンツ site-widthはメインコンテンツの横幅指定用-->
    <div id="contents" class="site-width">
        <section id="main">
            <div class="form-container">

                <form action="" method="post" class="form">
                    <p>ご指定のメールアドレスお送りした【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
                    <div class="area-msg">
                        <?php getErrMsg('common'); ?>
                    </div>
                    <label for="" class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
                        認証キー
                        <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo getErrMsg('token'); ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="再発行する">
                    </div>
                </form>
            </div>
            <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
        </section>
    </div>


<!-- footer -->
    <?php
    require('footer.php');
    ?>