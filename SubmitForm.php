<?php

$dsn = "**************";
$user = "*************";
$password = "***********";

$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS tbtest"  //tbtestが存在しなければ作成
     ." ("
     . "id INT AUTO_INCREMENT PRIMARY KEY," //主キー
     . "name char(32),"    //固定長文字列32バイトまでの文字列
     . "comment TEXT,"     //可変長文字列2^16 -1バイトまで
     . "date DATETIME,"   //日付時刻型(xxxx-xx-xx xx:xx:xx.xxxxxxx)
     . 'pass char(32)'
     .");";

$stmt = $pdo->query($sql);
?>

<?php

if(isset($_POST["Edit"])){
    //初期値設定
    $edit_name="Name";
    $edit_comment="Comment";
    $id = "";
    if(isset($_POST["edit_number"]) && $_POST["edit_number"] !=""){
        if(isset($_POST["edit_pass"]) && $_POST["edit_pass"] != ""){
            $id = intval($_POST["edit_number"]);
            $pass = $_POST["edit_pass"];

            //選択SQL文
            $sql = 'SELECT * from tbtest where id=:id and pass=:pass';
            
            $stmt = $pdo->prepare($sql);
            //バインド処理
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->execute();

            $results = $stmt->fetchAll();   //結果セットを配列で返す

            $match_num = $stmt->rowCount(); //直前に処理が行われた行の個数を返す
            if(!empty($results)){
                $row = $results[0];
                $edit_name = $row['name'];
                $edit_comment = $row['comment'];
            }else{
                $id="";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Submit form</title>
    <!-- CSSコード -->
    <style>
     form {
       /* フォームをページの中央に置く */
       /* フォームの範囲がわかるようにする */
       border: 1px solid #CCC;
       border-radius: 2em;

     }
     ul li{
       /*黒丸消去*/
       list-style: none;
     }
     body{
         //背景
       background-image: url(blueground.jpg);
       font-size:75%;
     }
     
     label {
       /* すべてのラベルを同じサイズにして、きちんと揃える */
       display: inline-block;
       width: 100px;
       text-align: right;
     }

     /*div { text-align : center ; }*/ 
    </style>
  </head>
  <body>
    <font color="#705DA8 ">
      <h1 id="midashi_1">6文字以上の動物</h1>
    </font>
    Send formのNameとCommentは初期値です。入力なしの場合は追記されません。<br>
    editでpass入力後,sendではpass入力不要です。動作確認のためpassは表示させています。<br>
    バグがあったら教えてもらえると嬉しいです。<br>
    <form method="POST" action="SubmitForm.php">
      <!--投稿追記用-->
        <h3>Send form</h3>
          <label for="comment">Name:</label>
          <input id="name" type="text" name="name" value="<?php if(isset($_POST["Edit"])){ echo $edit_name;}else{echo "Name";}?>" /><br>
          <label for="comment">Comment:</label>
          <input id="comment" type="text" name="comment" value="<?php if(isset($_POST["Edit"])){ echo $edit_comment;}else{echo "Comment";}?>"/><br>
        <!-- pass -->
          <label for="name_pass">Password:</label>
          <input id="name_pass" type="text" name="name_pass"/>
        <input type="submit" value="Send" name="Send" /><hr>
        
        <!--投稿削除用-->
        <h3>Delete form</h3>
        <label for="delete">Delete_Number:</label>
        <input id="delete_number" type="text" name="delete_number" /><br>
        <label for="delete_pass">Password:</label>
        <input id="delete_pass" type="text" name="delete_pass" />
        <input type="submit" value="Delete" name="Delete"/><hr>
        
        <!--投稿編集用-->
        <h3>Edit form</h3>
        <label for="edit">Edit_Number:</label>
        <input id="edit_number" type="edti_number" name="edit_number" /><br>
        
        <input id="textbox" type="hidden" name="textbox" value="<?php if(isset($_POST["Edit"])){echo $id;}?>"/>
        
        <label for="edit_pass">Password:</label>
        <input id="edit_pass" type="text" name="edit_pass" />
        <input type="submit" value="Edit" name="Edit" />
    </form>
    
  </body>
</html>

<?php
echo "<br>";
//Sendがclickされたとき
if(isset($_POST["Send"]) && $_POST["textbox"] != ""){
    //更新SQL文
    $sql = 'update tbtest set name=:name,comment=:comment where id=:id';
    
    $id = $_POST["textbox"]; //変更する投稿番号
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    
    echo $id."「".$name."」"."「".$comment."」編集を受け付けました。"."<br>";

    $stmt = $pdo->prepare($sql);

    //バインド処理
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->execute();  //SQL文を実行

//Editボタンが押されたとき
}elseif(isset($_POST["Edit"])){
    if($_POST["edit_number"] != ""){
        if($_POST["edit_pass"] != ""){
            if($match_num == 0){
                echo "Error:Pass not match"."<br>";
            }
        }else{
            echo "Error:Pass Empty"."<br>";
        }
    }else{
        echo "Error:Edit_Number Empty"."<br>";
    }
//Sendボタンが押されたとき
}elseif(isset($_POST["Send"])){
    if(isset($_POST["name"]) && isset($_POST["comment"])){
        if($_POST["name"]!="" && $_POST["comment"]!=""){
            if($_POST["name_pass"] != ""){

                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date('Y-m-d h:i:s');
                $pass = $_POST["name_pass"];
                echo "「".$name."」"."「".$comment."」送信を受け付けました。"."<br>";
                //挿入SQL文
                $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");

                $sql -> bindParam(':name', $name, PDO::PARAM_STR); //':name'に$nameを割当,型:string
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //':comment'に$commentを割当,型:string
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $sql -> execute();
            }else{
                echo "Error:pass empty"."<br>";
            }
        }else{
            echo "Error:Name or Comment Empty"."<br>";
        }
    }
//Deleteがclickされたとき
}elseif(isset($_POST["Delete"])){
    if(isset($_POST["delete_number"]) && $_POST["delete_number"]!="") {
        if($_POST["delete_pass"] != ""){

            $id = intval($_POST["delete_number"]);
            $pass = $_POST["delete_pass"];

            //削除SQL文
            $sql = 'DELETE from tbtest WHERE id=:id AND pass=:pass';

            $stmt = $pdo->prepare($sql);
            //バインド処理
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->execute();
            //直近の SQL ステートメントによって作用した行数を返す
            $match_num = $stmt->rowCount();

            if($match_num != 0){
                echo $id."の削除を受け付けました。"."<br>";
            }else{
                echo "Error:Pass not match"."<br>";
            }   
        }else{
            echo "Error:Pass Empty"."<br>";
        }
    }else{
        echo "Error:Delete_Number Empty"."<br>";
    }
}
$sql = 'SELECT * FROM tbtest'; //SQLステートメントの作成
$stmt = $pdo->query($sql);

$results = $stmt->fetchAll();
echo "----------------------------------------------------"."<br>";
foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].',';
    echo $row['pass'].'<br>';
    echo "<hr>";
}
?>
