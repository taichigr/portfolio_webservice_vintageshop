

   <?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


?>
   <?php require('auth.php'); ?>
   
    <?php 
    $siteTitle = 'マイページ'; 
    require('head.php');
    ?>
    <body class="page-login page-2colum page-logined">

        <!-- メニュー -->
        <?php
        require('header.php');
        ?>
         
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
        </p>
        
        <!-- メインコンテンツ -->
        <div class="contents site-width">

            <!-- Main -->
            <section id="main">
                <section class="list panel-list">
                    <h2 class="title">お気に入り商品</h2>
                    <a href="" class="panel">
                        <div class="panel-head">
                            <img src="img/sample-img.png" alt="商品タイトル">
                        </div>
                        <div class="panel-body">
                            <p class="panel-title">トレンチコート</p>
                        </div>
                    </a>
                    <a href="" class="panel">
                        <div class="panel-head">
                            <img src="img/sample-img.png" alt="商品タイトル">
                        </div>
                        <div class="panel-body">
                            <p class="panel-title">トレンチコート</p>
                        </div>
                    </a>
                    <a href="" class="panel">
                    <div class="panel-head">
                        <img src="img/sample-img.png" alt="商品タイトル">
                    </div>
                    <div class="panel-body">
                        <p class="panel-title">トレンチコート</p>
                    </div>
                    </a>
                    <a href="" class="panel">
                    <div class="panel-head">
                        <img src="img/sample-img.png" alt="商品タイトル">
                    </div>
                    <div class="panel-body">
                        <p class="panel-title">トレンチコート</p>
                    </div>
                    </a>
                </section>
                <section class="list panel-list">
                    <h2 class="title">購入済み商品</h2>
                    <a href="" class="panel">
                        <div class="panel-head">
                            <img src="img/sample-img.png" alt="商品タイトル">
                        </div>
                        <div class="panel-body">
                            <p class="panel-title">ダッフルコート</p>
                        </div>
                    </a>
                    <a href="" class="panel">
                        <div class="panel-head">
                            <img src="img/sample-img.png" alt="商品タイトル">
                        </div>
                        <div class="panel-body">
                            <p class="panel-title">Tシャツ</p>
                        </div>
                    </a>
                    <a href="" class="panel">
                        <div class="panel-head">
                            <img src="img/sample-img.png" alt="商品タイトル">
                        </div>
                        <div class="panel-body">
                            <p class="panel-title">トレンチコート</p>
                        </div>
                    </a>
                    
                </section>
            </section>
            
            <!-- サイドバー -->
            <?php 
            require('sidebar_mypage.php');
            ?>

        </div>

        <!-- footer -->
        <?php
        require('footer.php'); 
        ?>
