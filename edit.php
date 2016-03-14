<?php 

  define('PDO_DSN', 'mysql:dbname=myfriends;host=localhost');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');
  $dbh = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  $dbh->query('SET NAMES utf8');

  $sql = 'SELECT * FROM areas';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  $areas = array();

  while(1){

    //データを取得
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    if($rec == false){
      break;
    }

    $areas[] = $rec;
  }

  
  //編集する友達情報
  //編集する友達データを取得する
  $sql = sprintf("SELECT * FROM `friends` WHERE `friend_id` = %s", $_GET['friend_id']);
  //$sql = sprintf("SELECT * FROM `friends` WHERE `friend_id` = %s",$_GET['friend_id']);
  //SQLを実行する
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  //フェッチを行う ここの変数とHTMLの
  //こいつを呼び出す変数は同じになっていないといけない
  $friend = $stmt->fetch(PDO::FETCH_ASSOC);


  /*
   HW：ここをUpdate文の実行に変更しましょう
   POST 送信された情報を取得
   POST送信されたら、友達データを追加
  */
  if (isset($_POST) && !empty($_POST)){
    // var_dump($_POST['name']);
    // var_dump($_POST['area_id']);
    // var_dump($_POST['gender']);
    // var_dump($_POST['age']);
    // var_dump($_GET['friend_id']);

    //INSERT文作成
    // $sql = "UPDATE `friends` SET `friend_name`= '小林慶亮',`area_id`= 1,`gender`= 1,`age`= 10  WHERE friend_id = 46"; //値の決め打ち
    // %sと"%s"の使い分け方法："%s"の場合は文字列、%sの場合はそれ以外。　絶対integer型だとわかっている場合は%dでもいい。　こんな感じ??
    $sql = sprintf("UPDATE `friends` SET `friend_name`= '%s',`area_id`= %s,`gender`= %s,`age`= %s  WHERE friend_id = '%s'", 
          $_POST['name'], 
          $_POST['area_id'], 
          $_POST['gender'], 
          $_POST['age'], 
          $_GET['friend_id']
      );

    //SQL実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    //var_dump($stmt);

    /* show.phpに遷移する
      header()という遷移するための関数を使用する
      使い方： header('Location: 遷移したいページのパス');
      例：　header('Location: index.php');
    */
    //header('Location: show.php?area_id='.$area_id); // $area_id は編集した友達データのarea_idカラムに入っている値
    header('Location: show.php?area_id='.$_POST['area_id']);

    // この行以下のコードの処理を停止する
    // exit('これ以下の処理を行わず終了します')
    // 参考記事：http://liginc.co.jp/programmer/archives/1140
    exit();

  }

  //データベースから切断
  $dbh = null;

?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>myFriends</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>友達の編集</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- 名前 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">名前</label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="山田　太郎" value="<?php echo $friend['friend_name']; ?>">
              </div>
            </div>
            <!-- 出身 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">出身</label>
              <div class="col-sm-10">
                <select class="form-control" name="area_id">
                  <option value="0">出身地を選択</option>
                  <?php foreach ($areas as $area) { ?>
                      <!-- ifで、プルダウンに自分の出身県を入れることができる -->
                      <!-- elseで、プルダウンに自分以外の県を表示させる -->
                      <?php if ($area['area_id'] == $friend['area_id']) { ?>
                        <option value="<?php echo $area['area_id']; ?>" selected><?php echo $area['area_name']; ?></option>
                      <?php }else{ ?>
                        <option value="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></option>
                      <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <!-- 性別 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">性別</label>
              <div class="col-sm-10">
                <select class="form-control" name="gender">
                  <option value="0">性別を選択</option>
                  <option value="1" selected>男性</option>
                  <option value="2">女性</option>
                </select>
              </div>
            </div>
            <!-- 年齢 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">年齢</label>
              <div class="col-sm-10">
                <input type="text" name="age" class="form-control" placeholder="例：27" value="27">
              </div>
            </div>
          <input type="submit" class="btn btn-default" value="更新" onclick="index.php">
        </form>
        <input type="submit" class="btn btn-default" Value="都道府県一覧に戻る" onClick="history.go(-1);">
      </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
