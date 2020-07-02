
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF=8">
    <title>掲示板</title>
</head>

<body>

    <font color="red">
    <?php
        //データベースを開く
        $dsn = '******';
        $user = '*******';
        $password = '*******';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //tbtestがない場合新しくテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS tbtest"
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."date DATETIME,"
        ."pass char(18)"
        .");";
        $stmt = $pdo->query($sql);


        if(isset($_POST["submit"])){
            // 送信機能
        
            if (isset($_POST["name"])&& isset($_POST["comment"])&& isset($_POST["pass"])){ 
                if($_POST["edit_num_h"]!=""){
                    //編集
                    $id=$_POST["edit_num_h"];
                    $id=mb_convert_kana($id, 'a');//全角から半角

                    $name = $_POST['name'];
                    $comment = $_POST['comment'];
                    $date = date("Y/m/d H:i:s");
                    $pass = $_POST['pass'];

                    $sql = 'UPDATE tbtest SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $stmt -> execute();
                        
                    
                }else{
                    //投稿
                    $sql = $pdo -> prepare("INSERT INTO tbtest(name, comment,date, pass)  VALUES(:name,:comment,:date,:pass)");

                    $sql -> bindParam(':name', $name , PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

                    $name = $_POST['name'];
                    $comment = $_POST['comment'];
                    $date = date("Y/m/d H:i:s");
                    $pass = $_POST['pass'];
        
                    $sql -> execute();
                }
            }
        }
        
            
        //削除機能
        if(isset($_POST["del_com"])&&isset($_POST["del_ps"])){

            $id = $_POST["del_com"];
            $id=mb_convert_kana($id, 'a'); //全角から半角
            $del_ps = $_POST["del_ps"];
    
            $sql = $pdo ->prepare( "SELECT * FROM tbtest WHERE id = $id");

            //パスを取り出す
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> execute();
            $result = $sql -> fetch();
            $t_ps = $result['pass'];
    
            if($del_ps == $t_ps){ //パスワードが合ってたら
                $sql = "delete from tbtest where id =:id";
                $stmt = $pdo -> prepare($sql);
                $stmt ->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt ->execute();
            }else{
                echo "パスワードが違います";
                echo "<br>";
            }
        }

        //編集機能（編集ボタンが押されたら）
        if(isset($_POST["edit_num"]) && isset($_POST["edit_ps"])){
            $edit_num=$_POST["edit_num"];
            $edit_num=mb_convert_kana($edit_num , 'a');
            $edit_ps=$_POST["edit_ps"];

            $sql = $pdo ->prepare( "SELECT * FROM tbtest WHERE id = $edit_num");

            //情報を取り出す
            $sql -> bindParam(':name', $name , PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

            $sql -> execute();

            $result = $sql -> fetch();
            $t_ps = $result['pass'];

            //投稿フォームに書き写す
            if($edit_ps==$t_ps){ //パスワードが合ってたら
                $value_name=$result['name'];
                $value_com=$result['comment'];
                $value_num=$edit_num;
            }else{
                echo"パスワードが違います";
            }
        }else{ //押されていない時の投稿フォームのvalueを指定
            $value_name="名前";
            $value_com="コメント";
        }

    ?>
    </font>

    <!-- フォーム -->
    <div style="position: relative; height:150px;">
        <div style="position:relative; margin-left:30px;">
            <h3>投稿</h3>
            <form action="" method="post">
                <input type="text" name="name" value="<?= $value_name?>"><br>
                <input type="text" name="comment" value="<?= $value_com?>"><br>
                <input type="text" name="pass" value="パスワード">
                <input type="hidden" name="edit_num_h" value="<?php if(!empty($value_num)){echo $value_num;}?>">
                <input type="submit" name="submit">
            </form><br>
        </div>
        
        <div style="position:relative;bottom:165px;left:280px;">
            <h3>削除</h3>
            <form action="" method="post">
                <input type="text" name="del_com" value="削除対象番号"><br>
                <input type="text" name="del_ps" value="パスワード">
                <input type="submit" name="delete" value="削除">
            </form><br>
        </div>

        <div style="position:relative;bottom:305px; left:530px">
            <h3>編集</h3>
            <form action="" method="post">
                <input type="text" name="edit_num" value="編集対象番号"><br>
                <input type="text" name="edit_ps" value="パスワード">
                <input type="submit" name="edit" value="編集">
            </form><br>
        </div>
    </div>
    <!-- フォーム -->
    
    <div style="margin-left:30px;">
        <h3>コメント</h3>

        <?php
            //データベース表示
            $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            }

        ?>
    </div>
        
</body>
</html>