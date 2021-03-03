<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
    debug('POST送信されています。');
    // 例外処理
    try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
        $sql2 = 'UPDATE product SET delete_flg = 1 WHERE user_id = :u_id';
        $sql3 = 'UPDATE like SET delete_flg = 1 WHERE user_id = :u_id';
        // 流し込み
        $data = array(':u_id' => $_SESSION['user_id']);
        // クエリ実行
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);
        $stmt3 = queryPost($dbh, $sql3, $data);
        
        // クエリ成功の場合(＄stmt1だけでも成功していればOKとする)
        if($stmt1){
            debug('クエリ成功。ユーザーの退会成功。');
            //セッション削除
            session_destroy();
            //トップページへ遷移
            header("Location:index.php");
        }else{
            debug('クエリ失敗。ユーザーの退会失敗。');
            $err_msg['common'] = MSG07;
        }
    }catch (Exception $e){
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>


<?php
    $siteTitle = '退会';
    require('head.php');
?>

<body class="page-withdraw page-1colum">

    <!-- メニュー -->
    <?php
        require('header.php');
        ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        <!-- Main -->
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <h2 class="title">退会</h2>
                    <div class="area-msg">
                        <?php 
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                     ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" name="submit" class="btn btn-mid" value="退会する">
                    </div>
                </form>
            </div>
            <a href="mypage.php">&lt;マイページに戻る</a>
        </section>
    </div>










    <!-- フッター -->
    <?php
        require('footer.php');
?>
