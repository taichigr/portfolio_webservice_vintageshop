<?php 
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//DBから商品データを取得
$viewData = getProductOne($p_id);
debug('取得したDBデータ($viewData):'.print_r($viewData,true));
//パラメータに不正な値が入っているかチェック
if(empty($viewData)){
    error_log('エラー発生：指定ページに不正な値が入りました');
    header("Location:index.php");
}
debug('取得したDBデータ($viewData):'.print_r($viewData,true));


?>


<?php 
$siteTitle = '商品詳細';
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

                <div class="title">
                    <span class="badge"><?php echo sanitize($viewData['category']); ?></span> 
                    <?php echo sanitize($viewData['name']); ?>
                </div>
                
                <div class="product-img-container">
                    <div class="img-main">
                        <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像" id="switch-img-main">
                    </div>
                    <div class="img-sub">
                        <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像１：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                        <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像２：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                        <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="画像３：<?php echo sanitize($viewData['name']); ?>" class="js-switch-img-sub">
                    </div>
                </div>
                
                <div class="product-detail">
                    <p><?php echo sanitize($viewData['comment']); ?></p>
                </div>
                <div class="product-buy">
                    <div class="item-left">
                        <a href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt;商品一覧に戻る</a>
                    </div>
                    <form action="" method="post"> <!-- formタグを追加し、ボタンをinputに変更し、style追加 -->
                        <div class="item-right">
                            <input type="submit" value="買う!" name="submit" class="btn btn-primary" style="margin-top:0;">
                        </div>
                    </form>
                    <div class="item-right">
                        <p class="price">¥<?php echo sanitize(number_format($viewData['price'])); ?>-</p>
                    </div>
                </div>
            </section>

        </div>

        <!-- footer -->
        <?php 
        require('footer.php');
        ?>

    </body>
</html>