<?php
include('config.php');
session_start();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_SESSION['id'];

if(isset($_POST['figura'])){

$figuraHosta = $_POST['figura'];
$figuraGoscia = $_POST['figuraPrzeciwnika'];
$sql = "INSERT INTO figura (`id_gry`, `figura_hosta`, `figura_goscia`) VALUES ($id, '$figuraHosta', '$figuraGoscia');";
$sqlR = $dbh->prepare($sql);
$sqlR->execute();    
}

if(isset($_POST['pole1'])){


    $pole1 = $_POST['pole1'];
    $pole2 = $_POST['pole2'];
    $pole3 = $_POST['pole3'];
    $pole4 = $_POST['pole4'];
    $pole5 = $_POST['pole5'];
    $pole6 = $_POST['pole6'];
    $pole7 = $_POST['pole7'];
    $pole8 = $_POST['pole8'];
    $pole9 = $_POST['pole9'];



$pola = "SELECT * FROM rozgrywka WHERE id_lobby=$id;";
$polaR = $dbh->prepare($pola);
$polaR->execute();
$result = $polaR->fetch(PDO::FETCH_ASSOC);
if(!$result){
$rozgrywka = "INSERT INTO rozgrywka (`id_lobby`, `pole`, `pole2`, `pole3`, `pole4`, `pole5`, `pole6`, `pole7`, `pole8`, `pole9`) VALUES ($id, $pole1, $pole2, $pole3, $pole4, $pole5, $pole6, $pole7, $pole8, $pole9);";
$rozgrywkaR = $dbh->prepare($rozgrywka);
$rozgrywkaR->execute();
}
else{
    $rozgrywka2 = "UPDATE rozgrywka set `pole`=$pole1, `pole2`=$pole2, `pole3`=$pole3, `pole4`=$pole4, `pole5`=$pole5, `pole6`=$pole6, `pole7`=$pole7, `pole8`=$pole8, `pole9`=$pole9 WHERE id_lobby=$id;";
    $rozgrywkaR2 = $dbh->prepare($rozgrywka2);
    $rozgrywkaR2->execute(); 
}

if($_SESSION['user'] == 0) $zmianaTury = 1;
else if($_SESSION['user'] == 1) $zmianaTury = 0;

$tura = "UPDATE gra SET tura=$zmianaTury WHERE id=$id;";
$turaR = $dbh->prepare($tura);
$turaR->execute();

$czyjaTura = "SELECT tura FROM gra WHERE id=$id;";
$czyjaTuraR = $dbh->prepare($czyjaTura);
$czyjaTuraR->execute();
$czyjaTuraE = $czyjaTuraR->fetch(PDO::FETCH_ASSOC);
echo $czyjaTuraE['tura'];
}


if(isset($_POST['stan'])){

    $stan = "UPDATE gra SET stan=".$_POST['stan']." WHERE id=$id;";
    $stanR = $dbh->prepare($stan);
    $stanR->execute();

}
?>
