<?php
include('config.php');
session_start();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_POST['nazwa'])){

    $host = $_POST['nazwa'];
    $iph = $_POST['iph'];
    $tura = $_POST['tura'];
    $prywatna = $_POST['private'];
    $passwd = $_POST['passwd'];
    ;
    $crG = "INSERT INTO `gra` (`host`, `ip_hosta`, `tura`, `stan`, `prywatna`, `haslo`, `czas`)\n
VALUES (:host, :ip_hosta, $tura, 1, $prywatna, :passwd, CURRENT_TIMESTAMP() + INTERVAL 60 minute);";
     $crGR = $dbh->prepare($crG);
     $crGR->bindParam(':host', $host, PDO::PARAM_STR);
     $crGR->bindParam(':ip_hosta', $iph, PDO::PARAM_STR);
     $crGR->bindParam(':passwd', $passwd, PDO::PARAM_STR);
    $crGR->execute();

}

if(isset($_POST['stan'])){
$updt = "UPDATE gra SET stan = ".$_POST['stan']." WHERE id = ".$_SESSION['id'].";";
$updtR = $dbh->prepare($updt);
$updtR->execute();

$plansza = "INSERT INTO plansza (`id_gry`, `pionek_hosta`) VALUES (".$_SESSION['id'].", ".$_POST['pionek'].");";
$planszaR = $dbh->prepare($plansza);
$planszaR->execute();
}

?>