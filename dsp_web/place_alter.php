<?php

$db_path = "./ACCESS_MANAGE.db";

if(!empty($_POST["subscribe"])){
    $db = new SQLite3($db_path);
    $place_ID = htmlspecialchars($_POST["place_ID"]);
    $name = htmlspecialchars($_POST['place_name']);

    $sql = "INSERT INTO place_info(place_ID,place_name) values('$place_ID','$name')";
    $db->exec($sql);
    echo $name .'を登録しました。';

    $db->close();
}
if(!empty($_POST["confirm"])){

    $db = new SQLite3($db_path);
    $place_ID = htmlspecialchars($_POST["place_ID"]);
    $name = htmlspecialchars($_POST['user_name']);

    $sql = "DELETE FROM place_info where place_ID='$place_ID'";
    $db->exec($sql);
    echo $name .'を削除しました。';

    $db->close();
}


//now_table
$db = new SQLite3($db_path);
$sql = 'SELECT * FROM place_info';
$res = $db->query($sql);

$sql = 'SELECT COUNT(*) FROM place_info';
$max_rec_res = $db->query($sql);

$max_rec = $max_rec_res->fetchArray();
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
<title>場所登録画面</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
     <div class="row">
        現在のテーブル
        <table class="table table-bordered">
        <tr><th>Name</th><th>削除</th></tr>
         <?php
             while( $row = $res->fetchArray() ) {
                //echo $row[1];
                echo '<tr><td>' . $row[1] . '</td>';

                echo '<td><form id='. $row[0] .' action="place_alter.php" method="post">';
                echo '<input type="hidden" name="user_name" value="'. $row[1] .'">';
                echo '<input type="hidden" name="place_ID" value="'. $row[0] .'">';
                echo '<button type="submit" name="confirm" value="'. $row[0] .'">削除</button>';
                echo '</form></td>';

                echo '</tr>';

             }
             echo '</table>';
             echo '新規登録';
             echo '<form action="place_alter.php" method="post">';
             echo '<input type="hidden" name="place_ID" value='. $max_rec[0] .'>';
             echo '<input type="text" name="place_name">';
             echo '<button type="submit" name="subscribe" value="hoge">登録</button>';
             echo '</form>';
        ?>
    </div>
</div>
<a href="index.php">戻る</a>
<script type="text/javascript" src="js/confirm.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
