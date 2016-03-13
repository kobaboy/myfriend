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

  //POST 送信された情報を取得
  // POST送信されたら、友達データを追加
  if (isset($_POST) && !empty($_POST)){
    // var_dump($_POST['name']);
    //INSERT文作成
    $sql = sprintf("INSERT INTO `myfriends`.`friends` (`friend_id`, `friend_name`, `area_id`, `gender`, `age`, `created`) VALUES (NULL, '%s', '%s', '%s', '%s', now());",$_POST['name'],$_POST['area_id'],$_POST['gender'],$_POST['age']);
    //SQL実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }

  
    
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>myFriends</title>

    <!-- Bootstrap -->
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <link href="./assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="./assets/css/form.css" rel="stylesheet">
    <link href="./assets/css/timeline.css" rel="stylesheet">
    <link href="./assets/css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-facebook-square"></i> My friends</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>友達の登録</legend>
        <form method="post" action="" class="form-horizontal" role="form" action="new.php" >
            <!-- 名前 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">名前</label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="例：山田　太郎">
              </div>
            </div>
            <!-- 出身 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">出身</label>
              <div class="col-sm-10">
                <select class="form-control" name="area_id">
                  <!-- <option value="0">出身地を選択</option> -->
                  <!-- <option value="0" disabled selected>出身地を選択</option> -->
                  <option value="0" disabled selected>出身地を選択</option>
                  <?php foreach ($areas as $area) { ?>
                    <!-- valueに値を代入するためにechoを使用して配列の値を入れていく -->
                    <option value="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></option>
                  <?php } ?>
                  
                </select>
              </div>
            </div>
            <!-- 性別 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">性別</label>
              <div class="col-sm-10">
                <select class="form-control" name="gender">
                  <option value="0" disabled selected>性別を選択</option>
                  <option value="1">男性</option>
                  <option value="2">女性</option>
                </select>
              </div>
            </div>
            <!-- 年齢 -->
            <div class="form-group">
              <label class="col-sm-2 control-label">年齢</label>
              <div class="col-sm-10">
                <input type="text" name="age" class="form-control" placeholder="例：27">
              </div>
            </div>

          <input type="submit" class="btn btn-default" value="登録">
        </form>
        <input type="submit" class="btn btn-default" Value="戻る" onClick="history.go(-1);">
      </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>