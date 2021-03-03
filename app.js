$(function(){
    
    // フッターを最下部に固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
        $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }
    //メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
        $jsShowMsg.slideToggle('slow');
        setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
    }
    
    //画像ライブビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
    });
    $dropArea.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
    });
    $fileInput.on('change',function(e){
        $dropArea.css('border','none');
        var file = this.files[0], // 2. files配列にファイルが入っています
            $img = $(this).siblings('.prev-img'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
            fileReader = new FileReader(); //ファイルを読み込むFileReaderオブジェクト
        
        //5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
        fileReader.onload = function(event){
            //読み込んだデータをimgに設定
            $img.attr('src',event.target.result).show();
        };
        
        //6. 画像読み込み
        fileReader.readAsDataURL(file);
        
    });
    
    //テキストエリアカウント
    var $countUp = $('#js-count'),
        $countView = $('#js-count-view');
    $countUp.on('keyup',function(e){
        $countView.html($(this).val().length);
    });
    
});
