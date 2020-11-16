<?php

function com_send($mess){
    $mess_replace = str_replace(' ', ',', $mess);
    $mess_replace = str_replace('研究室', '', $mess_replace);
    $cmd = 'python3 post.py '.$mess_replace;
    echo exec($cmd);
    echo $mess_replace;
    // echo $cmd;
}

$db_path = "./ACCESS_MANAGE.db";
// $sql = null;
// $res = null;
// $row = null;
// $status = null;
// $now_date = null;
// $name = null;
// $logout_card_ID = null;
// $array = [];

if(!empty($_POST["log_out"])){
  $db = new SQLite3($db_path);
  $now_date=date("Y-m-d H:i:s.u");
  $value = htmlspecialchars($_POST["log_out"]);
  $name = htmlspecialchars($_POST['user_name']);

  $sql = "UPDATE user_inout SET logout='$now_date' where logout='' and card_ID='$value'";
  $db->exec($sql);
  echo $name .'さんが退出しました。';

  $db->close();
}

if(!empty($_POST["log_in"])){
  $db = new SQLite3($db_path);
  $now_date=date("Y-m-d H:i:s.u");
  $name = htmlspecialchars($_POST['user_name']);

  $sql = "SELECT card_ID FROM user_info WHERE user LIKE '$name%'";
  $card_res = $db->query($sql);
  $card_roc = $card_res->fetchArray();
  $sql = "SELECT * FROM user_info JOIN user_inout ON user_info.card_ID = user_inout.card_ID WHERE user_inout.logout='' AND user_inout.card_ID='$card_roc[0]'";
  $check_res = $db->query($sql);
  $check_roc = $check_res->fetchArray();

  if(empty($check_roc[0])){
      $sql = "INSERT INTO user_inout VALUES ('$card_roc[0]','$now_date','')";
      $db->exec($sql);
      echo $name .'さんがログインしました。';
  }else{
      echo $name .'さんはすでに入室記録してます';
  }

  $db->close();
}

if(!empty($_POST["now_place"])){
  $db = new SQLite3($db_path);
  // $now_date=date("Y-m-d H:i:s.u");
  $name = htmlspecialchars($_POST['place_name']);
  $value = htmlspecialchars($_POST['card_ID']);

  $sql = "UPDATE user_place SET place_ID='$name' WHERE card_ID='$value'";
  $db->exec($sql);


  $sql = "SELECT place_name FROM place_info WHERE place_ID ='$name'";
  $check_res = $db->query($sql);
  $place_name = $check_res->fetchArray();

  $sql = "SELECT user FROM user_info WHERE card_ID ='$value'";
  $check_res = $db->query($sql);
  $user_name = $check_res->fetchArray();

  echo $user_name[0] .'さんの場所を'. $place_name[0] .'に変更しました。';

  $db->close();
}


$db = new SQLite3($db_path);
//now_table
$sql = 'SELECT * FROM user_info JOIN user_inout ON user_info.card_ID = user_inout.card_ID JOIN user_place ON user_info.card_ID = user_place.card_ID JOIN place_info ON user_place.place_ID = place_info.place_ID where user_inout.logout="" ';
$res = $db->query($sql);

//name_list
$sql = 'SELECT user FROM user_info';
$name_list = $db->query($sql);

// $sql = 'select user_place.card_ID, place_info.place_name from user_place JOIN place_info ON user_place.place_ID = place_info.place_ID';
$sql = 'SELECT * FROM place_info ORDER BY place_ID ASC';
$place_list = $db->query($sql);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="stylesheet" href="css/main.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<title>タイトル</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<h1>
<div class="container">
     <div class="row">
        <table class="table table-bordered">
        <tr><th>Name</th><th>入室時間</th><th>場所</th><th>退出</th></tr>
         <?php
             while( $row = $res->fetchArray() ) {
                 //echo $row[1];
                 if ($row[4]==''){
                     //$status=0;

                     $array[] = [$row[0],$row[8]];
                     // $place_array[] = $row[8];

                     $str_time = date('Y/m/d H:i:s', strtotime($row[3]));
                     echo '<tr><td>' . $row[0] . '</td>';
                     echo '<td>' . $str_time . '</td>';
                     echo '<td>';
                     echo $row[8] .'　';
                     echo '<button type="button" class="replace" value='.$row[2].'>変更</button>';
                     echo '<div style="display:none" class='. $row[2] .'>';
                     echo '<form action="index.php" method="post">';
                     echo '<select name="place_name">';
                         while( $p_row = $place_list->fetchArray() ) {
                             echo '<option value='. $p_row[0] .'>'. $p_row[1] .'</option>';
                         }
                     echo '</select>';
                     echo '<input type="hidden" name="card_ID" value='. $row[2] .'>';
                     echo '<button type="submit" name="now_place" value="hoge">確定</button>';
                     echo '</form>';
                     echo '</div>';
                     // echo '</div>';

                     echo '</td>';
                     echo '<td><form action="index.php" method="post">';
                     echo '<input type="hidden" name="user_name" value='. $row[0] .'>';
                     echo '<button type="submit" name="log_out" value='. $row[2] .'>退出</button>';
                     echo '</form></td>';
                     echo '</tr>';
                 }
             }
             foreach($array as $test){
               $tmp = implode(' ', $test);
               $tmp_array[] = $tmp;
             }
             if (count($tmp_array)){
               $str = implode('.',$tmp_array);

               com_send($str);
             }else{
               com_send("誰もいませんよ。。");
             }

        ?>
        </table>


        <form action="index.php" method="post">
        <select name='user_name'>
        <?php
            while( $row = $name_list->fetchArray() ) {
                echo '<option value='. $row[0] .'>'. $row[0] .'</option>';
            }
        ?>
        </select>
        <button type="submit" name="log_in" value='hoge'>入室</button>
        </form>
    </div>
</div>
<a href="chart.php">統計</a><br>
<a href="gantt.php">さらに詳細な統計</a><br>
<a href="alter.php">新規登録・変更・削除</a><br>
<a href="place_alter.php">場所新規登録・削除</a><br>
<a href="t_lab_gantt.php">シークレット</a><br>

</h1>
<script type="text/javascript" src="js/button.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
