<?php 

// login_dateがある場合はログイン済ユーザーとみなす
if(!empty($_SESSION['login_date'])){
    debug('ログイン済ユーザーです。');
    // 現在日時が最終ログイン日時＋有効期限を超えていた場合
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバーです。');
        // セッションを削除（ログアウト）
        session_destroy();
        // ログインページへ
        header("Location:login.php");
    }else{
        debug('ログイン有効期限以内です。');
        $_SESSION['login_date'] = time();
        
        //現在のページがログインの時のみマイページへ遷移する
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('マイページに遷移します。');
            header("Location:mypage.php");
            exit;
        }
    }
}else{
    //login_dateがなかった場合はログインしていないユーザーとみなす
    debug('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        debug('ログインページに遷移します。');
        header("Location:login.php");
        exit;
    }
}
