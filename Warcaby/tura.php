<?php
include('config.php');
session_start();

$czyjaTura = "SELECT tura FROM gra WHERE id=".$_SESSION['id'].";";
$czyjaTuraR = $dbh->prepare($czyjaTura);
$czyjaTuraR->execute();
$tura = $czyjaTuraR->fetch(PDO::FETCH_ASSOC);
echo $tura['tura'];
?>


