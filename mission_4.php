<?php
/*https://www.php.net/manual/ja/book.pdo.php のphp公式レファレンスを参考にしました。
こちらのサイトにPDOクラスの定数や関数が全て書いてあります。*/
$dsn = "mysql:dbname=tech-base;host=localhost";
$user = "root";
$password = "wonder578";

/*
PDOクラスのインスタンス$pdoを生成
new PDO(データベース接続文字列,接続ユーザ名,接続時のパスワード,接続オプション)
接続オプションPDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING...警告を発生
他オプション多数あり*/
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS tbtest"  //tbtestが存在しなければ作成
     ." ("
     . "id INT AUTO_INCREMENT PRIMARY KEY," //主キー
     . "name char(32),"    //char(32)固定長文字列32バイトまでの文字列
     . "comment TEXT"     //TEXT可変長文字列2^16 -1バイトまで
     .");";

$stmt = $pdo->query($sql);
/*
この辺はおそらく、オブジェクト指向プログラミングをやったことがないと理解しづらいかも。
私も体系的に説明できません。
$stmt = $pdo->query()はSQLステートメントを実行し、結果をPDOStatementオブジェクトとして返す。

query()はPDOクラスのメソッド。上記レファレンスにもPOD::queryという形で書いてあるはずです。
失敗した場合は$stmtにfalseが入る

今回の文では$stmtにPDOStatementオブジェクトが格納される。
*/

$sql ='SHOW TABLES';  //SHOW TABLESのSQLstatement
//$resultにquery($sql)を実行して得られたPDOStatementオブジェクトを格納
//配列はオブジェクトともいえる。
$result = $pdo -> query($sql);

foreach ($result as $row){
    //echo var_dump($row);
    echo $row[0];
    echo '<br>';
}
echo "<hr>";

$sql ='SHOW CREATE TABLE tbtest';
$result = $pdo -> query($sql);
foreach ($result as $row){
    //echo var_dump($row);
    //echo $row[0];
    echo $row[1];
}
echo "<hr>";

/*
PDO::prepareは、PDOstatement::execute()メソッドによって実行されるSQlステートメントを準備する。
PDO::prepareの返り値はPDOstatementオブジェクト。
PDOstatement::execute()...今まで準備してきたSQLステートメントを実行させる

$sql->execute()の返り値はbool型。成功:TRUE,失敗:FALSE
動作確認は,$変数名 = $sql->execute();echo $変数名;
などで行えます。

ちなみに、query($sql)と、prepare($sql)とexecute()の組み合わせはどちらもSQL文を実行した
結果を返すメソッドです。何が違うかというと、query()ではname=:nameなどのバインド処理が出来ないのに対し、
prpare($sql)-execute()ではバインド処理を間に挟めることが出来るところに違いがあります。
*/

$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment) VALUES (:name, :comment)");

/*
ここで上記コードより、$sqlはPDOstatementオブジェクト。
bindParamはPDOstatementクラスのメソッド。
上記レファレンスにもPDOStatement::bindParamと書いてあるはずです。

PDOStatement::bindParam ― 指定されたphp変数名にDBのパラメータをバインド(割当て)する

PDOStatement::bindParam ($parameter ,$variable ,$data_type) : bool(返り値)

$parameter:':name'や':comment'...これはCreate文で名前付けされたもの
$variable:$parameterと対応付けさせたい変数
$data_type: ex) PDO::PARAM_STR(文字列型),PDO::PARAM_INT(整数型)
*/

$sql -> bindParam(':name', $name, PDO::PARAM_STR); //':name'に$nameを割当,型:string
$sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //':comment'に$commentを割当,型:string

$name = 'RYUKI';
$comment = 'honor';

$sql -> execute();


$sql = 'SELECT * FROM tbtest'; //SQLステートメントの作成

//SQLステートメント($sql)を実行し、結果をPDOStatementオブジェクトとして返し、$stmtへ
$stmt = $pdo->query($sql);

/*
PDOstatement::fetchAll...全ての結果行を含む配列を返す
つまり、$sqlのSQL文を実行して、得られた結果を配列の形で返すということ。
該当するものがない場合は空の配列。SQL文が失敗している場合はFALSEが返る。
今回の例では、SELECT * FROM tbtestなので、
tbtestの全ての行を配列の形で返しています。
配列といってもここでは連想配列として返されているのではないのかなと思います。
*/
$results = $stmt->fetchAll();

foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    //連想配列の値を一つずつ出力
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].'<br>';
	echo "<hr>";
}


/*以下は削除処理・更新処理ですが、単にSQL文を変えただけです。
どちらもバインド(id=:id...etc)が必要なので、PDO::prepare PDOStatement::executeの
組み合わせを使っています。*/

$id = 1; //変更する投稿番号
$name = "norse";
$comment = "eye";
$sql = 'update tbtest set name=:name,comment=:comment where id=:id';  //SQL文
$stmt = $pdo->prepare($sql);   //返り値であるPDOStatementオブジェクトを$stmtへ

//変数名とパラメータ名を対応付ける
$stmt->bindParam(':name', $name, PDO::PARAM_STR);
$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();  //SQL文を実行

$id = 2;
$sql = 'delete from tbtest where id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

/****4-1********************************************************
$dsnの式の中にスペースを入れないこと！
以下注釈のためコード内に含める必要はありません。
  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)とは、データベース操作で発生したエラーを
  警告として表示してくれる設定をするための要素です。
  デフォルトでは、PDOのデータベース操作で発生したエラーは何も表示されません。
  その場合、不具合の原因を見つけるのに時間がかかってしまうので、このオプションはつけておきましょう。
*****************************************************************/


/****4-2*********************************************************
  IF NOT EXISTSを入れないと２回目以降にこのプログラムを呼び出した際に、
  SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'tbtest' already exists
  という警告が発生します。これは、既に存在するテーブルを作成しようとした際に発生するエラーです。
*****************************************************************/
?>
