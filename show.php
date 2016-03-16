<?php 

  //データベースに接続
  define('PDO_DSN', 'mysql:dbname=myfriends;host=localhost');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');
  $dbh = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  $dbh->query('SET NAMES utf8');

  //ページ読み込み時にURLのパラメーター上でactionがあれば処理をする
  if(isset($_GET['action']) && !empty($_GET['action'])){
    // actionパラメーターの値はdeleteであれば削除処処理を実行する
    if($_GET['action'] == 'delete'){
      //  実際の削除処理
      // $sql = 'DELETE FROM  `テーブル名`WHERE 削除したいレコードの条件';
      // 削除すり処理の場合、一件の単位がレコードなのでカラムを指定する必要はない、
      // どのレコードを削除するかをprimary keyであるidで指定して削除する。
      $sql = 'DELETE FROM `friends` WHERE `friend_id` = ' .$_GET['friend_id'];
      $stmt = $dbh->prepare($sql);
      $stmt->execute();

      header('Location: index.php');
    }
  }

  // $_GETはGET送信のときに使用される変数 ----- ここはareasのテーブルの処理 ------
  $sql = sprintf('SELECT * FROM areas WHERE area_id=%s', $_GET['area_id']);

  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  $rec = $stmt->fetch(PDO::FETCH_ASSOC);
  //var_dump($rec);

  $area_name = $rec['area_name'];
  //-----------------------------------------------------------------------

  //友達リストを表示するための ----------- ここはfriendsテーブルの処理 --
  $sql = sprintf('SELECT * FROM friends WHERE area_id=%s', $_GET['area_id']);
  //SQL文を実行するためのSQL文
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  //---------------------------------------------------------------

  //取得データ格納用Array
  $friends = array(); //配列の初期化 --------------まずここで配列の初期化を行う


  //男女のカウント用変数
  $male = 0;
  $female = 0;  

  //配列に値を保存しておいていつでも値を取り出せるようにする
  while(1){
    //データを取得する
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    if($rec == false){
      //データ取得の末尾まで到達したので繰り返しの処理を終了する
      break;
    }

    //データを格納する 
    $friends[] = $rec; //--------------------ここでフェッチした値をfriend[]に格納していく


    //ここで男女数をカウントする
    if($rec['gender'] == 1){
      $male++;
    }
    else if($rec['gender'] == 2){
      $female++;
    }
  }

  //平均年令取得
  // $sql = 'SELECT `gender`, TRUNCATE(AVG(`age`), 2) AS avgAge
  //         FROM `friends` WHERE `area_id` ='
  //         . $_GET['area_id'].
  //         'GROUP BY `gender';
  $sql = 'SELECT `gender`, TRUNCATE(AVG(`age`), 2) AS avgAge FROM `friends` WHERE `area_id` = '
          . $_GET['area_id']
          . ' GROUP BY `gender`';


  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  $avgAge = array();

  while(1){
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if($rec == false){
      break;
    }
    $avgAge[] = $rec;
  }

  // echo '<pre>';
  // var_dump($avgAge);
  // echo '</pre>';

  //var_dump($friends);
  //var_dump($male);
  //var_dump($female);
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

    <script type="text/javascript">
      //confirm()内のokボタンを押すとtrueを、cancelボタンを押すとfalseを返す
      function destroy(friend_id){
        if (confirm('FRIEND ID:' + friend_id +  'の友達を削除しますか') == true){
          //confirm(hoge); これで変数hogeの中身を表示させることができる
          // var fuga = 'ふが';
          // console.log(friend_id); //phpのecho文のような役割
          //ページをリロードしdelete処理を実行するための記述  JSの文字連結は+である。
          location.href = 'show.php?action=delete&friend_id=' + friend_id;
          return true;
        }        
        //キャンセルボタンを押したとき
        else{
          return false;
        }
      }
    </script>

  </head>
  <body>
    
    <div class="container">
      <div class="row">
        <!-- []の名前をブランケットという -->
        <div class="col-md-4 content-margin-top">
        <legend><?php echo $area_name; ?>の友達</legend>
        <!-- <div class="well">男性：2名　女性：1名</div> -->

        <div class="well">男性：<?php echo $male; ?>名　女性<?php echo $female; ?>名
        <br>
          <!-- 男女の平均年齢を出力する -->
          <?php 
            
            if(empty($avgAge[0]['gender'])){  //if(empty()){  これでもいける
              //男女の友達がいない場合
              echo '男性平均: --歳　';
              echo '女性平均: --歳';
            }
            else if(!empty($avgAge[0]['gender']) and !empty($avgAge[1]['gender'])){  //else if(!empty($avgAge[1])){ 　これでもいける
              //男女の友達がいる場合
              echo '男性平均:', $avgAge[0]['avgAge'], '歳　';
              echo '女性平均:', $avgAge[1]['avgAge'], '歳';
            }
            else if($avgAge[0]['gender'] == 2){
              //女性友達のみの場合
              echo '男性平均: --歳　';
              echo '女性平均:', $avgAge[0]['avgAge'], '歳';
            }
            else{
              //男性友達のみの場合
              echo '男性平均:', $avgAge[0]['avgAge'], '歳　';
              echo '女性平均: --歳';
            }
            
           ?>
        </div>
        <table class="table table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th><div class="text-center">名前</div></th>
              <th><div class="text-center"></div>操作</th>
            </tr>
          </thead>
          <tbody>
            <!-- 友達の名前を表示 -->
             <?php foreach ($friends as $friend) { ?>
               <tr>
                 <td><div class="text-center"><?php echo $friend['friend_name']; ?></div></td>
                 <td>
                   <div class="text-center">
                     <a href="edit.php?friend_id=<?php echo $friend['friend_id']; ?>"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                     <!-- <a href="javascript:void(0);" onclick="destroy();"><i class="fa fa-trash"></i></a> -->
                     <a href="#" onclick="destroy(<?php echo $friend['friend_id']; ?>);"><i class="fa fa-trash"></i></a>
                     <!-- aタグやbuttonタグなどユーザーが押せるタグにonClickを指定することでjavascriptのコードを発動することができる -->
                     <!-- 今回はjavascript内で定義するdestroy関数にfriend_idを渡した上来で処理を実行する -->
                   </div>
                 </td>
               </tr>
             <?php } ?>
          </tbody>
          </table>

          <input type="button" class="btn btn-default" value="新規作成" onClick="location.href='new.php'">  
          <input type="button" class="btn btn-default" value="都道府県一覧画面に戻る" onClick="location.href='index.php'">

        </div>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>