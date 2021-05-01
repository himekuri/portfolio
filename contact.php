<?php
session_start();

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// トークン生成
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = sha1(random_bytes(30));
}

// HTML特殊文字をエスケープする関数
function h($str) {
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

//メールヘッダインジェクション対策のための改行削除関数
function i( $str ) {
return str_replace( array( "\r", "\n" ), '', $str );
}


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // POSTでのアクセスでない場合
    $name = '';
    $mail = '';
    $content = '';
    $err_msg = '';
    $complete_msg = '';

} else {
    // フォームがサブミットされた場合（POST処理）
    $token = $_POST[ 'token' ]; //送信されたトークンを変数に格納
    if ( !( hash_equals( $token, $_SESSION[ 'token' ] ) && !empty( $token ) ) ) {
        //送信されたトークンがセッションと同じ値か比較して異なる場合
        $err_msg = '不正アクセスの可能性があります。';
        exit();
        
    } else { 
        //トークンが一致した場合
        // 入力された値を取得する
        $name = $_POST['name'];
        $mail = $_POST['mail'];
        $content = $_POST['content'];
        
        $name = h( $name );
        $mail = h( $mail );
        $mail = i( $mail );
        $content = h( $content );
        
    
        // エラーメッセージ・完了メッセージの用意
        $errors = [];
        $complete_msg = '';
    
        // 空チェック
        if ($name == '' || $mail == '' || $content == '') {
            $errors[] = '全ての項目を入力してください。';
        }
        
        //emailの形式が正しいか
        if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = '正しいEメールアドレスを指定してください。';
        }
        
        //内容が255文字以内か
        if (mb_strlen($_POST['content']) > 255) {
            $errors[] = 'お問い合わせ内容は255文字以内でお願いします。';
        }
        
        // エラーなし
        if (empty($errors)) {
            $to = 'bib1hannak1@gmail.com'; 
            $headers = "From: " . $mail . "\r\n";
    
            // 本文の最後に名前を追加
            $content .= "\r\n\r\n" . $name;
            
            //タイトル
            $subject = 'ポートフォリオからお問い合わせがありました';
            
            // メール送信
            mb_send_mail($to,$subject, $content, $headers);
    
            // 完了メッセージ
            $complete_msg = '送信されました！';
    
            // 全てクリア
            $name = '';
            $mail = '';
            $content = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>お問い合わせ | Yamada Mahiro のポートフォリオサイト</title>
    
    <!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	<link rel="stylesheet" href="css/main.css">
</head>

<body>
    <section id="contact" class="section bg-light">
		<h2 class="heading">Contact</h2>
		<div class="container">
		    <div class="form-message">
		        <p>何かありましたら、お気軽にお問い合わせくださいませ。</p>
                <p>下記フォームよりご記入ください。項目は全て必ずご記入お願いします。</p>
		    </div>
		    
		    <?php if (!empty($errors)): ?>
		        <?php foreach ($errors as $msg): ?>
                    <div class="alert alert-danger">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($complete_msg != ''): ?>
                <div class="alert alert-success">
                    <?php echo $complete_msg; ?>
                </div>
            <?php endif; ?>
            
			<form method="post" class="form">
                <div class="row form-group">
                    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                    <!-- 576px以上の画面幅のとき、ラベルは3つ分のカラム幅で表示する -->
                    <label class="col-sm-3 col-form-label">氏名：</label>

                    <!-- 576px以上の画面幅のとき、フォーム部品は9つ分のカラム幅で表示する -->
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-3 col-form-label">メール：</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="mail">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-3 col-form-label">内容：</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="content"></textarea>
                    </div>
                </div>
                <div class="row form-group">
                    <!-- 576px以上の画面幅のとき、3つ分のカラム幅の隙間を表示 -->
                    <!-- 576px以上の画面幅のとき、9つ分のカラム幅で送信ボタンを表示 -->
                    <div class="offset-sm-3 col-sm-9">
                        <button type="submit" class="btn btn-info col-sm-12">お問い合わせ内容を送信する</button>
                    </div>
                </div>
            </form>
            <div class="transition-btn">
            	<a class="btn btn-outline-dark" href="/index.html">トップページに戻る</i></a>
            </div>
		</div>
	</section>
	<footer>
		<div class="container">
			<p class="copyright">©2021 Yamada Mahiro</p>
		</div>
	</footer>

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

	<!-- Popper.js,Bootstrap JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- その他のライブラリのJavaScript -->
	<script src="js/vendor/jquery.waypoints.min.js"></script>
	<script src="js/vendor/mobile-detect.min.js"></script>

	<script src="js/main.js"></script>
	
	<!-- Font Awesome -->
    <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
</body>
</html>