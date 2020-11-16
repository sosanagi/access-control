<?php
$db_path = "./ACCESS_MANAGE.db";

$db = new SQLite3($db_path);
$sql = 'SELECT user_info.user, user_inout.login, user_inout.logout FROM user_info JOIN user_inout ON user_info.card_ID = user_inout.card_ID WHERE user_inout.logout!="" ORDER BY user_info.user' ;
$res = $db->query($sql);
$user_data= array();
$in_data=array();
$out_data=array();
$tmp_in_data=array();
$tmp_out_data=array();

//$sql = 'SELECT user FROM user_info WHERE NOT user="勝藤 輝"' ;
$sql = 'SELECT user FROM user_info' ;
$user_list = $db->query($sql);

while ($user_array = $user_list->fetchArray()){
  array_push($user_data, $user_array[0]);
}
foreach ($user_data as $value) {
  while( $row = $res->fetchArray() ) {
    if ($value == $row[0]) {
      array_push($tmp_in_data,$row[1]);
      array_push($tmp_out_data,$row[2]);
    }
  }
  $in_data[$value] = $tmp_in_data;
  $out_data[$value] = $tmp_out_data;
  $tmp_in_data=[];
  $tmp_out_data=[];
}
// var_dump($inout_data);
$user_json = json_encode($user_data);
$in_json = json_encode($in_data);
$out_json = json_encode($out_data);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>勤怠表</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=IE8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="css/style.css" type="text/css" rel="stylesheet">
        <link href="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css" rel="stylesheet" type="text/css">
        <link href="css/gantt.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <div class="container">

            <h2 id="example">
                勤怠表
            </h2>
            <h4>
                時間マークで今日の日付へ転移。+,-ボタンでより詳細な時刻が見られます。
            </h4>
            <!-- <table class="table table-bordered">
            <tr><th>Name</th><th>入室時間</th><th>場所</th></tr>
             <?php
                while( $row = $res->fetchArray() ) {
                  $str_login_time = date('Y/m/d H:i:s', strtotime($row[1]));
                  $str_logout_time = date('Y/m/d H:i:s', strtotime($row[2]));
                  echo '<tr><td>' . $row[0] . '</td>';
                  echo '<td>' . $str_login_time . '</td>';
                  echo '<td>' . $str_logout_time . '</td>';
                  echo '</tr>';
                }
            ?>
            </table> -->
            <div class="gantt"></div>
            <h2><a href="index.php">戻る</a></h2>
        </div>
    <script type="text/javascript">
      let user_json = <?php echo $user_json; ?>;
      let in_json = <?php echo $in_json; ?>;
      let out_json = <?php echo $out_json; ?>;
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="js/jquery.fn.gantt.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
    <script src="js/gantt.js"></script>
    </body>
</html>
