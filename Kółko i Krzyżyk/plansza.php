<?php
include('config.php');
session_start();

$id = $_SESSION['id'];
$figura = $_SESSION['figura'];
$plansza = "SELECT rozgrywka.*, g.tura FROM `rozgrywka` INNER JOIN gra g ON rozgrywka.id_lobby = g.id WHERE rozgrywka.id_lobby = $id;";
$planszaR = $dbh->prepare($plansza);
$planszaR->execute();
$planszaE = $planszaR->fetch(PDO::FETCH_ASSOC);



$pola = [];

    if($planszaE['pole'] == 0) $pola[0] = '<i class="fas fa-times"></i>';
    else if($planszaE['pole'] ==1) $pola[0] = '<i class="far fa-circle"></i>';
    else $pola[0] = '';

for($i = 2; $i<=9; $i++){
    if($planszaE['pole'.$i] == 0) $pola[$i] = '<i class="fas fa-times"></i>';
    else if($planszaE['pole'.$i] ==1) $pola[$i] = '<i class="far fa-circle"></i>';
    else $pola[$i] = '';
}

echo '<center><h2>Grasz: '.$figura.'</h2></center>
<table class="game-table">
<tbody>
<tr><td id="1" class="game">'.$pola[0].'</td><td id="2" class="game">'.$pola[2].'</td><td id="3" class="game">'.$pola[3].'</td></tr>
<tr><td id="4" class="game">'.$pola[4].'</td><td id="5" class="game">'.$pola[5].'</td><td id="6" class="game">'.$pola[6].'</td></tr>
<tr><td id="7" class="game">'.$pola[7].'</td><td id="8" class="game">'.$pola[8].'</td><td id="9" class="game">'.$pola[9].'</td></tr>
</tbody>
</table>'.$planszaE['tura'];
?>




