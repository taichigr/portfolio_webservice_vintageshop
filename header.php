<header>
    <div>
        <div class="title-container">
            <h1><a href="index.php">Vintage shop</a></h1>
        </div>
        <div class="nav-container">
            <nav id="top-nav">
                <ul>
                   <?php 
                    if(empty($_SESSION['user_id'])){ 
                    ?>
                    <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
                    <li><a href="login.php">ログイン</a></li>
                   <?php
                    }else{
                    ?>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li><a href="logout.php">ログアウト</a></li>
                   <?php
                              } 
                    ?>
                    
                </ul>
            </nav>
        </div>
    </div>
</header>