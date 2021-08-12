<?php
include('config.php');
session_start();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
@$id = $_SESSION['id'];
@$ruch = $_POST['ruch'];
if($ruch){
    $updt = "UPDATE plansza SET `plansza`='$ruch' WHERE id_gry = $id;";
    $updtR = $dbh->prepare($updt);
    $updtR->execute();
}


if(isset($_POST['tura'])){
  $czyjaTura = ($_POST['tura'] == 'false') ? 0 : 1;
$tura = "UPDATE gra SET tura=".$czyjaTura." WHERE id=".$_SESSION['id'].";";
$turaR = $dbh->prepare($tura);
$turaR->execute();
echo $_POST['tura'];
}

?>


