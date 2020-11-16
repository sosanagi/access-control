<?php

$db_path = "./ACCESS_MANAGE.db";

if(!empty($_POST["subscribe"])){
    $db = new SQLite3($db_path);
    $card_ID = htmlspecialchars($_POST["card_ID"]);
    $name = htmlspecialchars($_POST['user_name']);


    $sql = "UPDATE user_info SET user='$name' where card_ID='$card_ID'";
    $db->exec($sql);
    echo $name .'を登録変更しました。';

    $db->close();
}
if(!empty($_POST["confirm"])){

    $db = new SQLite3($db_path);
    $card_ID = htmlspecialchars($_POST["card_ID"]);
    $name = htmlspecialchars($_POST['user_name']);

    $sql = "DELETE FROM user_info where card_ID='$card_ID'";
    $db->exec($sql);
    echo $name .'を削除しました。';

    $db->close();
}


//now_table
$db = new SQLite3($db_path);
$sql = 'SELECT * FROM user_info';
$res = $db->query($sql);

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
<title>カード登録画面</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
     <div class="row">
        <table class="table table-bordered">
        <tr><th>Name</th><th>変更</th><th>削除</th></tr>
         <?php
             while( $row = $res->fetchArray() ) {
                //echo $row[1];
                echo '<tr><td>' . $row[0] . '</td>';

                echo '<td><form action="alter.php" method="post">';
                echo '<input type="hidden" name="card_ID" value='. $row[1] .'>';
                echo '<input type="text" name="user_name">';
                echo '<button type="submit" name="subscribe" value="hoge">変更</button>';
                echo '</form></td>';

                echo '<td><form id='. $row[1] .' action="alter.php" method="post">';
                echo '<input type="hidden" name="user_name" value="'. $row[0] .'">';
                echo '<input type="hidden" name="card_ID" value="'. $row[1] .'">';
                echo '<button type="submit" name="confirm" value="'. $row[1] .'">削除</button>';
                echo '</form></td>';

                echo '</tr>';

             }
        ?>
        </table>
    </div>
</div>
<a href="index.php">戻る</a>
<script type="text/javascript" src="js/confirm.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
