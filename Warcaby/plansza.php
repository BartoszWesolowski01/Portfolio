<?php
include('config.php');
session_start();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
@$id = $_SESSION['id'];
if($id){
    $loadP = "SELECT * FROM `plansza` WHERE id_gry = $id;";
    $loadR = $dbh->prepare($loadP);
    $loadR->execute();
    $planszaE = $loadR->fetch(PDO::FETCH_ASSOC);

    $plansza = [];
$plansza = str_split($planszaE['plansza']);

if(@$plansza != str_split(@$_POST['plansza'])) echo json_encode(@$plansza);

}
if(isset($_POST['startPlansza'])){
    $updtPlansza = "UPDATE plansza SET plansza='".$_POST['startPlansza']."' WHERE id_gry=".$_SESSION['id'].";";
    $updtPlanszaR = $dbh->prepare($updtPlansza);
    $updtPlanszaR->execute();
    }
?>