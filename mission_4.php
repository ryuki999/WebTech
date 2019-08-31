<?php
/*https://www.php.net/manual/ja/book.pdo.php ��php�������t�@�����X���Q�l�ɂ��܂����B
������̃T�C�g��PDO�N���X�̒萔��֐����S�ď����Ă���܂��B*/
$dsn = "mysql:dbname=tech-base;host=localhost";
$user = "root";
$password = "wonder578";

/*
PDO�N���X�̃C���X�^���X$pdo�𐶐�
new PDO(�f�[�^�x�[�X�ڑ�������,�ڑ����[�U��,�ڑ����̃p�X���[�h,�ڑ��I�v�V����)
�ڑ��I�v�V����PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING...�x���𔭐�
���I�v�V������������*/
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS tbtest"  //tbtest�����݂��Ȃ���΍쐬
     ." ("
     . "id INT AUTO_INCREMENT PRIMARY KEY," //��L�[
     . "name char(32),"    //char(32)�Œ蒷������32�o�C�g�܂ł̕�����
     . "comment TEXT"     //TEXT�ϒ�������2^16 -1�o�C�g�܂�
     .");";

$stmt = $pdo->query($sql);
/*
���̕ӂ͂����炭�A�I�u�W�F�N�g�w���v���O���~���O����������Ƃ��Ȃ��Ɨ������Â炢�����B
�����̌n�I�ɐ����ł��܂���B
$stmt = $pdo->query()��SQL�X�e�[�g�����g�����s���A���ʂ�PDOStatement�I�u�W�F�N�g�Ƃ��ĕԂ��B

query()��PDO�N���X�̃��\�b�h�B��L���t�@�����X�ɂ�POD::query�Ƃ����`�ŏ����Ă���͂��ł��B
���s�����ꍇ��$stmt��false������

����̕��ł�$stmt��PDOStatement�I�u�W�F�N�g���i�[�����B
*/

$sql ='SHOW TABLES';  //SHOW TABLES��SQLstatement
//$result��query($sql)�����s���ē���ꂽPDOStatement�I�u�W�F�N�g���i�[
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
PDO::prepare�́APDOstatement::execute()���\�b�h�ɂ���Ď��s�����SQl�X�e�[�g�����g����������B
PDO::prepare�̕Ԃ�l��PDOstatement�I�u�W�F�N�g�B
PDOstatement::execute()...���܂ŏ������Ă���SQL�X�e�[�g�����g�����s������

$sql->execute()�̕Ԃ�l��bool�^�B����:TRUE,���s:FALSE
����m�F��,$�ϐ��� = $sql->execute();echo $�ϐ���;
�Ȃǂōs���܂��B

���Ȃ݂ɁAquery($sql)�ƁAprepare($sql)��execute()�̑g�ݍ��킹�͂ǂ����SQL�������s����
���ʂ�Ԃ����\�b�h�ł��B�����Ⴄ���Ƃ����ƁAquery()�ł�name=:name�Ȃǂ̃o�C���h�������o���Ȃ��̂ɑ΂��A
prpare($sql)-execute()�ł̓o�C���h�������Ԃɋ��߂邱�Ƃ��o����Ƃ���ɈႢ������܂��B
*/

$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment) VALUES (:name, :comment)");

/*
�����ŏ�L�R�[�h���A$sql��PDOstatement�I�u�W�F�N�g�B
bindParam��PDOstatement�N���X�̃��\�b�h�B
��L���t�@�����X�ɂ�PDOStatement::bindParam�Ə����Ă���͂��ł��B

PDOStatement::bindParam �\ �w�肳�ꂽphp�ϐ�����DB�̃p�����[�^���o�C���h(������)����

PDOStatement::bindParam ($parameter ,$variable ,$data_type) : bool(�Ԃ�l)

$parameter:':name'��':comment'...�����Create���Ŗ��O�t�����ꂽ����
$variable:$parameter�ƑΉ��t�����������ϐ�
$data_type: ex) PDO::PARAM_STR(������^),PDO::PARAM_INT(�����^)
*/

$sql -> bindParam(':name', $name, PDO::PARAM_STR); //':name'��$name������,�^:string
$sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //':comment'��$comment������,�^:string

$name = 'RYUKI';
$comment = 'honor';

$sql -> execute();


$sql = 'SELECT * FROM tbtest'; //SQL�X�e�[�g�����g�̍쐬

//SQL�X�e�[�g�����g($sql)�����s���A���ʂ�PDOStatement�I�u�W�F�N�g�Ƃ��ĕԂ��A$stmt��
$stmt = $pdo->query($sql);

/*
PDOstatement::fetchAll...�S�Ă̌��ʍs���܂ޔz���Ԃ�
�܂�A$sql��SQL�������s���āA����ꂽ���ʂ�z��̌`�ŕԂ��Ƃ������ƁB
�Y��������̂��Ȃ��ꍇ�͋�̔z��BSQL�������s���Ă���ꍇ��FALSE���Ԃ�B
����̗�ł́ASELECT * FROM tbtest�Ȃ̂ŁA
tbtest�̑S�Ă̍s��z��̌`�ŕԂ��Ă��܂��B
�z��Ƃ����Ă������ł͘A�z�z��Ƃ��ĕԂ���Ă���̂ł͂Ȃ��̂��ȂƎv���܂��B
*/
$results = $stmt->fetchAll();

foreach ($results as $row){
    //$row�̒��ɂ̓e�[�u���̃J������������
    //�A�z�z��̒l������o��
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].'<br>';
	echo "<hr>";
}


/*�ȉ��͍폜�����E�X�V�����ł����A�P��SQL����ς��������ł��B
�ǂ�����o�C���h(id=:id...etc)���K�v�Ȃ̂ŁAPDO::prepare PDOStatement::execute��
�g�ݍ��킹���g���Ă��܂��B*/

$id = 1; //�ύX���铊�e�ԍ�
$name = "norse";
$comment = "eye";
$sql = 'update tbtest set name=:name,comment=:comment where id=:id';  //SQL��
$stmt = $pdo->prepare($sql);   //�Ԃ�l�ł���PDOStatement�I�u�W�F�N�g��$stmt��

//�ϐ����ƃp�����[�^����Ή��t����
$stmt->bindParam(':name', $name, PDO::PARAM_STR);
$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();  //SQL�������s

$id = 2;
$sql = 'delete from tbtest where id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

/****4-1********************************************************
$dsn�̎��̒��ɃX�y�[�X�����Ȃ����ƁI
�ȉ����߂̂��߃R�[�h���Ɋ܂߂�K�v�͂���܂���B
  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)�Ƃ́A�f�[�^�x�[�X����Ŕ��������G���[��
  �x���Ƃ��ĕ\�����Ă����ݒ�����邽�߂̗v�f�ł��B
  �f�t�H���g�ł́APDO�̃f�[�^�x�[�X����Ŕ��������G���[�͉����\������܂���B
  ���̏ꍇ�A�s��̌�����������̂Ɏ��Ԃ��������Ă��܂��̂ŁA���̃I�v�V�����͂��Ă����܂��傤�B
*****************************************************************/


/****4-2*********************************************************
  IF NOT EXISTS�����Ȃ��ƂQ��ڈȍ~�ɂ��̃v���O�������Ăяo�����ۂɁA
  SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'tbtest' already exists
  �Ƃ����x�����������܂��B����́A���ɑ��݂���e�[�u�����쐬���悤�Ƃ����ۂɔ�������G���[�ł��B
*****************************************************************/
?>