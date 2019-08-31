<!mission_3-5 古川流輝>
<?php
if(isset($_POST["Edit"])){
  $edit_name="Name";
  $edit_comment="Comment";
  $edit_pass = $_POST["edit_pass"];
  $edit_number = "";
  if(isset($_POST["edit_number"])){
    $edit_number = $_POST["edit_number"];
    $filename = "mission_3-5.txt";
    $file = file($filename);
    foreach ($file as $fline){
      $line = explode("<>",$fline);
      if($line[0] == $edit_number && $line[4] == $_POST["edit_pass"]){ 
        $edit_name = $line[1];
        $edit_comment = $line[2];
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
    <form method="POST" action="mission_3-5.php">
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
        
        <input id="textbox" type="hidden" name="textbox" value="<?php if(isset($_POST["Edit"])){echo $edit_number;}else{echo "";}?>"/>
        
        <label for="edit_pass">Password:</label>
        <input id="edit_pass" type="text" name="edit_pass" />
        <input type="submit" value="Edit" name="Edit" />
        <!--<textarea name="dumy" cols="50" rows="5"> </textarea>--!>
    </form>
    
  </body>
</html>

<?php
//txtファイル出力
function txt_print(){
  $filename = "mission_3-5.txt";
  $fp = fopen($filename ,"a+") or die("ファイルを開けませんでした。");
  $file = file($filename);
  //配列に格納された行を順に処理
  print "----------------------------------------------------------------------------"."<br>";
  foreach ($file as $fline){
    $line = explode("<>",$fline);
    for($i = 0;$i < count($line)-1;$i++){
      echo $line[$i]." ";
    }
    print "<br>";
  }
}

//Sendがclickされたとき
if(isset($_POST["Send"]) && $_POST["textbox"] != ""){
  $name = $_POST["name"];
  $message = $_POST["comment"];
  echo $_POST["textbox"]."「".$name."」"."「".$message."」編集を受け付けました。"."<br>";
  
  $filename = "mission_3-5.txt";
  $file = file($filename);

  //編集番号が指定されているとき
  $fp = fopen($filename ,"w+") or die("ファイルを開けませんでした。");
  $edit_number = $_POST["textbox"];
  foreach ($file as $fline){
    $line = explode("<>",$fline);
    if($line[0] == $edit_number){
      $post_line = $line[0]."<>".$name."<>".$message."<>".date('Y-m-d h:i:s')."<>".$line[4]."<>"."\n";
      fwrite( $fp , $post_line);
    }else{
      fwrite( $fp ,$fline);
    }
  }
  
  fclose($fp);

}elseif(isset($_POST["Edit"])){
  if($_POST["edit_number"] == ""){
    echo "Error:Edit_Number Empty"."<br>";
  }
}elseif(isset($_POST["Send"])){
  if(isset($_POST["name"]) && isset($_POST["comment"])){
    if($_POST["name"]!="" && $_POST["comment"]!=""){
      if($_POST["name_pass"] != ""){
        $name = $_POST["name"];
        $message = $_POST["comment"];
        $pass = $_POST["name_pass"];
        echo "「".$name."」"."「".$message."」送信を受け付けました。"."<br>";

        $filename = "mission_3-5.txt";
        $file = file($filename);
        
        $fp = fopen($filename ,"a+") or die("ファイルを開けませんでした。");
        //ファイルの最終投稿番号search
        $end_file = explode("<>",end($file));
        if($end_file[0]==""){
          $count = 1;
        }else{
          $count = $end_file[0] + 1;
        }
        //保存フォーマットtranslate
        $post_line = $count."<>".$name."<>".$message."<>".date('Y-m-d H:i:s')."<>".$pass."<>"."\n";
        
        fputs( $fp , $post_line);
        fclose( $fp );
      }else{
        echo "Error:pass not match"."<br>";
      }
    }
  }
  //Deleteがclickされたとき
}elseif(isset($_POST["Delete"])){
  if(isset($_POST["delete_number"]) && $_POST["delete_number"]!="") {
    if($_POST["delete_pass"] != ""){
      $delete_number = $_POST["delete_number"];
      $pass = $_POST["delete_pass"];
      echo $delete_number."の削除を受け付けました。"."<br>";
      $filename = "mission_3-5.txt";
      $file = file($filename);
      $fp = fopen($filename ,"w+") or die("ファイルを開けませんでした。");

      foreach ($file as $fline){
        $line = explode("<>",$fline);
        if($line[0] != $delete_number || $line[4] != $pass){ 
          fwrite($fp,$fline);
        }
      }
      fclose($fp);
    }else{
      echo "Error:pass not match"."<br>";
    }
  }else{
    echo "Error:Delete_Number Empty"."<br>";
  }
}
  txt_print();
?>
