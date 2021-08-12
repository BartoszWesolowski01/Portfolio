<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php
include('config.php');
session_start();
error_reporting(0);

function getIPAddress() {  
    
     if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
    
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
     }  

    else{  
             $ip = $_SERVER['REMOTE_ADDR'];  
     }  
     return $ip;  
}  
$ip = getIPAddress();  

$_SESSION['ip'] = $ip;

$x = 9;
$color = '#E8D0AA';
$color2 = '#A67D5D';
$zmiana = '';

$_SESSION['id'] = @$_GET['id'];

if(isset($_SESSION['id'])){
    $prGM = "SELECT * FROM gra WHERE id=".$_SESSION['id'].";";
    $prGMR = $dbh->prepare($prGM);
    $prGMR->execute();
    $gameInfo = $prGMR->fetch(PDO::FETCH_ASSOC);

    $_SESSION['kto'] = ($ip == $gameInfo['ip_hosta']) ? false : true;


    $pionki = "SELECT * FROM plansza WHERE id_gry=".$_SESSION['id'].";";
$pionkiR = $dbh->prepare($pionki);
$pionkiR->execute();
$pionkiE = $pionkiR->fetch(PDO::FETCH_ASSOC); 
     }

 





     if(@$_SESSION['ip']){
        if($gameInfo['ip_goscia'] != null){
            if($_SESSION['ip'] != $gameInfo['ip_goscia'] && $_SESSION['ip'] != $gameInfo['ip_hosta']) echo "<script type='text/javascript'>alert('Ta gra jest pełna, poszukaj innej'); document.location='warcaby.php'</script>";
        
        }
        else if($gameInfo['ip_goscia'] == null){
    
if(isset($_GET['id'])){
            if($_SESSION['kto'] == true){
                $dodajGoscia = "UPDATE gra SET ip_goscia='$ip' WHERE id=".$_GET['id'].";";
                $dodajGosciaR = $dbh->prepare($dodajGoscia);
                $dodajGosciaR->execute();
            }
        }
    }
}

if($_SESSION['kto'] == true){
    if($gameInfo['prywatna'] == 1){
 echo ($gameInfo['gosc'] == null) ? "<script type='text/javascript'>if(prompt('Podaj hasło') != '".$gameInfo['haslo']."'){ alert('Podano nieprawidłowe hasło.'); location.reload(); } 
       else { 
        alert('Podano prawidłowe hasło');
        var nick = prompt('Podaj nick'); 
        if(nick.length == 0){ alert('Musisz podać swój nick!'); location.reload(); }
        else { $.ajax({ method: 'POST', url: 'warcaby.php?id=".$_GET['id']."', data: { 'nick' : nick }, success: function(){ alert('Powodzenia, '+nick+'!'); location.reload(); } }); }
       </script>" : "";
}
else{
    echo ($gameInfo['gosc'] == null) ? "<script type='text/javascript'>
     var nick = prompt('Podaj nick'); 
     if(nick.length == 0){ alert('Musisz podać swój nick!'); location.reload(); }
     else { $.ajax({ method: 'POST', url: 'warcaby.php?id=".$_GET['id']."', data: { 'nick' : nick }, success: function(){ alert('Powodzenia, '+nick+'!'); location.reload(); } }); }
    </script>" : "";  
}
if(isset($_POST['nick'])){
        $dodajGoscia = "UPDATE gra SET `gosc`='".$_POST['nick']."', ip_goscia='$ip' WHERE id=".$_GET['id'].";";
        $dodajGosciaR = $dbh->prepare($dodajGoscia);
        $dodajGosciaR->execute();
}
}   




    

?>

<script type="text/javascript">
$(document).ready(function(){
 var czarny = 'rgb(77, 46, 13)';
 var bialy = 'rgb(247, 193, 71)';
 var plansza = [];
 var cachePlanszy = [];
 var kto = <?php echo ($_SESSION['ip'] == @$gameInfo['ip_hosta']) ? 'false' : 'true'; ?>;
 var pionek;
 var pionekPrzeciwnika;
 var tura;
 var czyja;
 var stan = <?php echo (@$gameInfo['stan']) ? $gameInfo['stan'] : 0; ?>;
 var currentID;
 var currentPawn;
 var host = [];
 var gosc = [];
 var possibleMoves = [];
 var allPossibleMoves = [];
 var damki = [];
 var warunkiZbicia = [];
 var doZbicia = [];
 var ruchPrzyZbiciu = [];
 var pionkiKtoreMogaBic = [];
 var czyjePionki;
 var pionkiPrzeciwnika;

    czas = '<?php echo (@$gameInfo['czas']) ? @$gameInfo['czas'] : 0 ?>';
    nazwa_hosta = '<?php echo (@$gameInfo['host']) ? @$gameInfo['host'] : 0 ?>';
    nazwa_goscia = '<?php if(@$gameInfo['gosc'] == "") echo "Oczekiwanie na gracza"; else echo @$gameInfo['gosc'] ?>';

    pionek = <?php if(@$pionkiE) { if(@$pionkiE['pionek_hosta'] == 0) { echo ($_SESSION['ip'] == $gameInfo['ip_hosta']) ? 'bialy' : 'czarny'; } else if(@$pionkiE['pionek_hosta'] == 1){ echo ($_SESSION['ip'] == $gameInfo['ip_hosta']) ? 'czarny' : 'bialy';} } else echo 0; ?>;
    pionekPrzeciwnika = (pionek == bialy) ? czarny : bialy;

 const possibilites = [-11, -9, 11, 9];
 const indexOfAll = (arr, val) => arr.reduce((acc, el, i) => (el === val ? [...acc, i] : acc), []);



 function Menu(){

    $(".join").click(function(){
document.location = 'warcaby.php?id='+$(this).val();
});

    $("body").append('<form action="" method="POST"><input type="text" name="nazwa" placeholder="Nick">\
    Prywatna gra? <input type="checkbox" name="private">\
    </br><input type="button" value="Stwórz poczekalnię" name="createLobby" disabled="disabled"></form>');
 
$("input[type='checkbox']").change(function(){

    if($(this).prop("checked") == true) $("body form").append('<input type="password" name="passwd" placeholder="Hasło do poczekalni" id="passwd">'); 
    else $("#passwd").remove(); 

});
$("input[name='nazwa']").keyup(function(){
if($(this).val().length > 0) $("input[name='createLobby']").removeAttr("disabled");
else $('input[name="createLobby"]').attr('disabled','disabled');
});


$("input[name='createLobby']").click(function(){
    var nazwa = $("body input[name='nazwa']").val();
    var password = $("body input[name='passwd']").val();
    var private = 0;
    if(password) private = 1;
    tura = (Math.random()>=0.5)? true : false;
    var iph = '<?php echo $_SESSION['ip']; ?>';
 $.ajax({
     method: "POST",
     url: "prepareGame.php",
     data: {
         'iph' : iph,
         "tura" : tura,
        "nazwa" : nazwa,
        "private" : private,
        "passwd" : password
     },
     success: function(){
         location.reload();
     }
 });
});
}

if(stan == 0) Menu();





function Rozgrywka(){
$("body").css("background-color", "black");
 function zbicia(){
    var kolor;
    (pionek == bialy) ? kolor = czarny : kolor = bialy;
    
for(var k = 0; k < czyjePionki.length; k++){



var minus9 = $("#"+($("."+czyjePionki[k]).parent().attr('id')-9));
var minus18 = $("#"+($("."+czyjePionki[k]).parent().attr('id')-18));

var minus11 = $("#"+($("."+czyjePionki[k]).parent().attr('id')-11));
var minus22 = $("#"+($("."+czyjePionki[k]).parent().attr('id')-22));

var plus9 = $("#"+(parseInt($("."+czyjePionki[k]).parent().attr('id'))+9));
var plus18 = $("#"+(parseInt($("."+czyjePionki[k]).parent().attr('id'))+18));

var plus11 = $("#"+(parseInt($("."+czyjePionki[k]).parent().attr('id'))+11));
var plus22 = $("#"+(parseInt($("."+czyjePionki[k]).parent().attr('id'))+22));




    if(czyjePionki[k].toString().search("0") > -1){

       if(minus9.children().css('background-color') == kolor && minus18.html() == "" && minus18.css("background-color") != 'rgb(232, 208, 170)') { 
        doZbicia.push(parseInt($(minus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(plus11.children().css('background-color') == kolor && plus22.html() == "" && plus22.css("background-color") != 'rgb(232, 208, 170)'){
        doZbicia.push(parseInt($(plus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

    }
    
    if(czyjePionki[k].toString().indexOf("9") == 1 || czyjePionki[k].toString() == "9"){
        if(minus11.children().css('background-color') == kolor && minus22.html() == "" && minus22.css("background-color") != 'rgb(232, 208, 170)'){ 
        doZbicia.push(parseInt($(minus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(plus9.children().css('background-color') == kolor && plus18.html() == "" && plus18.css("background-color") != 'rgb(232, 208, 170)'){
      
        doZbicia.push(parseInt($(plus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }
    }
   
    if(czyjePionki[k].toString() == "1" || czyjePionki[k].toString().indexOf("1") == 1) {
        
 if(minus9.children().css('background-color') == kolor && minus18.html() == "" && minus18.css("background-color") != 'rgb(232, 208, 170)'){ 
        doZbicia.push(parseInt($(minus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(plus11.children().css('background-color') == kolor && plus22.html() == "" && plus22.css("background-color") != 'rgb(232, 208, 170)'){
    
        doZbicia.push(parseInt($(plus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
    }
    }

if(czyjePionki[k].toString().indexOf("8") == 1){
    if(minus11.children().css('background-color') == kolor && minus22.html() == "" && minus22.css("background-color") != 'rgb(232, 208, 170)') { 
        doZbicia.push(parseInt($(minus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(plus9.children().css('background-color') == kolor && plus18.html() == "" && plus18.css("background-color") != 'rgb(232, 208, 170)'){
        doZbicia.push(parseInt($(plus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
}
}



    if((czyjePionki[k].toString().search("0") == -1 && czyjePionki[k].toString().search("9") == -1 && czyjePionki[k].toString().search("1") == -1 && czyjePionki[k].toString().search("8") == -1) || czyjePionki[k].toString().indexOf("9") == 0){

        if(minus9.children().css('background-color') == kolor && minus18.html() == "" && minus18.css("background-color") != 'rgb(232, 208, 170)') { 
        doZbicia.push(parseInt($(minus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(minus11.children().css('background-color') == kolor && minus22.html() == "" && minus22.css("background-color") != 'rgb(232, 208, 170)') { 
        doZbicia.push(parseInt($(minus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(minus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }

       if(plus9.children().css('background-color') == kolor && plus18.html() == "" && plus18.css("background-color") != 'rgb(232, 208, 170)'){
        doZbicia.push(parseInt($(plus9).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus18).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }
       if(plus11.children().css('background-color') == kolor && plus22.html() == "" && plus22.css("background-color") != 'rgb(232, 208, 170)'){
        doZbicia.push(parseInt($(plus11).children().attr("class")));
        ruchPrzyZbiciu.push(parseInt($(plus22).attr("id")));
        if(pionkiKtoreMogaBic.indexOf(czyjePionki[k]) == -1) pionkiKtoreMogaBic.push(czyjePionki[k]);
       }
     


    }
   
    if(kto == false){
    if(czyjePionki[k].toString().indexOf("0") == 1 || czyjePionki[k] == "0"){
        if($("#"+(parseInt(czyjePionki[k])-9)).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])-9);
    }
    else if(czyjePionki[k].toString().indexOf("9") == 1 || czyjePionki[k] == "9"){
        if($("#"+(parseInt(czyjePionki[k])-11)).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])-11); 
    }
    else{
        for(var l = 0; l < 2; l++){
         if($("#"+(parseInt(czyjePionki[k])+parseInt(possibilites[l]))).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])+possibilites[l]);
        }
    }
    }
    else{
    if(czyjePionki[k].toString().indexOf("0") == 1 || czyjePionki[k] == "0"){
        if($("#"+(parseInt(czyjePionki[k])+11)).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])+11);
    }
    else if(czyjePionki[k].toString().indexOf("9") == 1 || czyjePionki[k] == "9"){
        if($("#"+(parseInt(czyjePionki[k])+9)).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])+9); 
    }
    else{
        for(var l = 2; l < 4; l++){
         if($("#"+(parseInt(czyjePionki[k])+parseInt(possibilites[l]))).html() == "") allPossibleMoves.push(parseInt(czyjePionki[k])+possibilites[l]);
        }
    }
    }

    if(k == czyjePionki.length-1 && doZbicia.length == 0 && allPossibleMoves.length == 0){
      alert("Przegrałeś"); plansza = [];
    }
    else if(k == czyjePionki.length-1 && allPossibleMoves.length > 0) allPossibleMoves = [];
    
}


     


for(var h = 0; h < doZbicia.length; h++){
  
    $("."+doZbicia[h]).css("border-color", "red");
    $("#"+ruchPrzyZbiciu[h]).css("border", "green 2px solid");
    $("."+pionkiKtoreMogaBic[h]).css("border-color", "green");
}
if(pionkiKtoreMogaBic.length > 0){
  
for(var g = 0; g<czyjePionki.length; g++){
    $("."+czyjePionki[g]).css("pointer-events", "none");
    if(pionkiKtoreMogaBic.indexOf(czyjePionki[g]) > -1){
        $("."+czyjePionki[g]).css("pointer-events", "auto");
    }
}
}
 }


 $.ajax({
            method: 'POST',
            url: 'plansza.php',
            dataType: 'json',
            data: {
                'plansza' : plansza.toString()
            },
            success: function(data){
                if(data.length > 0){ 
                    
                    plansza = data;
                }
            }
        });

setInterval(function(){
    if(plansza.length == 0){
    
$.each($("td"), function(x){
        if(x<39 && $(this).css('background-color') == 'rgb(166, 125, 93)') { $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #4d2e0d; border: 2px white solid; border-radius: 50%;"></div>');  plansza[x] = 'g'; }
        if(x>60 && $(this).css('background-color') == 'rgb(166, 125, 93)') { $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #F7C147; border: 2px black solid; border-radius: 50%;"></div>');  plansza[x] = 'h'; }
        if(x>=39 && x<=60 || $(this).css('background-color') == 'rgb(232, 208, 170)') { $(this).css("pointer-events", "none"); plansza[x] = "-"; }
    });


if(plansza.length == 100){
    $.ajax({
            method: 'POST',
            url: "plansza.php",
            async: false,
            data: {
                'startPlansza' : plansza.join(''),
            },
            success: function(){
                cachePlanszy = plansza;
            }
            });
        }
    }
    else{
       
        $.ajax({
            method: 'POST',
            url: 'plansza.php',
            dataType: 'json',
            data: {
                'plansza' : plansza.toString()
            },
            success: function(data){

                if(data.length > 0){ 
                  
                    plansza = data;
                }
                }
        });
       if(plansza.toString() != cachePlanszy.toString()){
          
        $("td").html('');
    $.each($("td"), function(x){
     
        if(plansza[x] == 'g' && $(this).css('background-color') == 'rgb(166, 125, 93)') { if(kto == true) $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: '+pionek+'; border: 2px #47bf8b solid; border-radius: 50%;"></div>'); else $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: '+pionekPrzeciwnika+'; border: 2px #47bf8b solid; border-radius: 50%;"></div>'); }  
        if(plansza[x] == 'h' && $(this).css('background-color') == 'rgb(166, 125, 93)') { if(kto == false) $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: '+pionek+'; border: 2px #47bf8b solid; border-radius: 50%;"></div>');  else $(this).html('<div class="'+x+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: '+pionekPrzeciwnika+'; border: 2px #47bf8b solid; border-radius: 50%;"></div>'); }  
        if(plansza[x] == "-" && $(this).css('background-color') == 'rgb(166, 125, 93)') { $(this).html(""); }
        if($(this).css('background-color') == 'rgb(232, 208, 170)') $(this).css("pointer-events", "none");
        cachePlanszy = plansza;
    });
       }
   

    }
  

    $.ajax({
        method: 'POST',
        url: 'tura.php',
        success: function(data){
            data = data.trim();
                tura = (data == 0) ? false : true;
               
                if((kto == false && tura == false) || (kto == true && tura == true)) czyja = "Twoja Tura";
                if((kto == true && tura == false) || (kto == false && tura == true)) czyja = "Tura Przeciwnika";
               
                $("#tura").html(czyja);
        }
    });


    host = indexOfAll(plansza, 'h');
    gosc = indexOfAll(plansza, 'g');
 
    czyjePionki = (kto == false) ? host : gosc;
    
    pionkiPrzeciwnika = (czyjePionki == host) ? gosc : host;

    if(plansza.length == 100){
      
    if(host.length == 0){
        alert("Host Przegrywa!"); plansza = [];
    }
    if(gosc.length == 0){
        alert("Gość Przegrywa!"); plansza = [];
    }
    }
    if((kto == false && tura == false) || (kto == true && tura == true)){



    if(doZbicia.length == 0) zbicia();
   
    

 
    
    for(var j = 0; j < pionkiPrzeciwnika.length; j++){
      
        $("#"+pionkiPrzeciwnika[j]).css("pointer-events", "none");
    }
  



$("div").click(function(){
    if(currentPawn) $('.'+currentPawn).css("border-color", "#47bf8b");
   
    if(possibleMoves.length > 0){
        for(var r = 0; r < possibleMoves.length; r++){
          $("#"+possibleMoves[r]).css('border', '');
            if(r == possibleMoves.length-1) possibleMoves = [];
      }
    }

    currentPawn = $(this).attr('class');
    currentID = $(this).parent().attr('id');
    $(this).css("border-color", "blue");

    if(ruchPrzyZbiciu == 0){
        if(kto == false){
    if(currentID.indexOf("0") == 1 || currentID == "0"){
        if($("#"+(parseInt(currentID)-9)).html() == "") possibleMoves.push(parseInt(currentID)-9);
    }
    else if(currentID.indexOf("9") == 1 || currentID == "9"){
        if($("#"+(parseInt(currentID)-11)).html() == "") possibleMoves.push(parseInt(currentID)-11); 
    }
    else{
        for(var l = 0; l < 2; l++){
         if($("#"+(parseInt(currentID)+parseInt(possibilites[l]))).html() == "") possibleMoves.push(parseInt(currentID)+possibilites[l]);
        }
    }
}
else{
    if(currentID.indexOf("0") == 1 || currentID == "0"){
        if($("#"+(parseInt(currentID)+11)).html() == "") possibleMoves.push(parseInt(currentID)+11);
    }
    else if(currentID.indexOf("9") == 1 || currentID == "9"){
        if($("#"+(parseInt(currentID)+9)).html() == "") possibleMoves.push(parseInt(currentID)+9); 
    }
    else{
        for(var l = 2; l < 4; l++){
         if($("#"+(parseInt(currentID)+parseInt(possibilites[l]))).html() == "") possibleMoves.push(parseInt(currentID)+possibilites[l]);
        }
    } 
}
    
    for(var x = 0; x < possibleMoves.length; x++){
        $("#"+possibleMoves[x]).css('border', 'green 2px solid');
    }

    }
   
});


 
$("td").click(function(){
if(currentPawn != undefined && $(this).html() == "" && (ruchPrzyZbiciu.indexOf(parseInt($(this).attr('id')))  > -1 || possibleMoves.indexOf(parseInt($(this).attr('id'))) > -1)) {
    plansza[currentID] = "-"; $("#"+currentID).html(""); currentID = $(this).attr('id');
    (kto == false) ? plansza[currentID] = 'h' : plansza[currentID] = 'g';
    (pionek == bialy) ? $("#"+currentID).html('<div class="'+currentPawn+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #F7C147; border: 2px #47bf8b solid; border-radius: 50%;"></div>') : $("#"+currentID).html('<div class="'+currentPawn+'" style="height: 40px; width: 40px; margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #4d2e0d; border: 2px white solid; border-radius: 50%;"></div>');
      currentPawn = ""; currentID = "";

      if(ruchPrzyZbiciu.indexOf(parseInt($(this).attr('id')))  > -1){
       
        plansza[doZbicia[ruchPrzyZbiciu.indexOf(parseInt($(this).attr('id')))]] = "-";

          for(var n = 0; n<ruchPrzyZbiciu.length; n++){
            $("#"+ruchPrzyZbiciu[n]).css("border", "");
          }
        ruchPrzyZbiciu = [];
        doZbicia = [];
        pionkiKtoreMogaBic = [];
    }
if((kto == false && tura == false) || (kto == true && tura == true) || tura == ""){
    $.ajax({
            method: 'POST',
            url: "ruch.php",
            data: {
                'ruch' : plansza.join(''),
                'tura' : !tura
            },
            success: function(data){
                data = data.trim();
                tura = (data == "false") ? false : true;
                
                if((kto == false && tura == false) || (kto == true && tura == true)) czyja = "Twoja Tura";
                if((kto == true && tura == false) || (kto == false && tura == true)) czyja = "Tura Przeciwnika";
               
                $("#tura").html(czyja);
            }
        });
    }
    
 
    for(var f = 0; f < possibleMoves.length; f++){
          $("#"+possibleMoves[f]).css('border', '');
          if(f == possibleMoves.length-1) possibleMoves = [];
      }
      

    }
});

    }

}, 1000);

if((kto == false && tura == false) || (kto == true && tura == true)) czyja = "Twoja Tura";
if((kto == true && tura == false) || (kto == false && tura == true)) czyja = "Tura Przeciwnika";
$("#tura").html(czyja);


}
if(stan == 2) Rozgrywka();

function Lobby(){
  if(stan == 1){
    setInterval(function(){
$.ajax({
    method: 'GET',
    url: 'warcaby.php?id=<?php echo $_SESSION['id']; ?>',
    success: function(data){
     $("body").html(data);
    }
});
    }, 5000);
  }
var pionekHosta;
var pionekGoscia;
pionekHosta = (pionek == bialy) ? "Pionek Biały" : "Pionek Czarny";  
pionekGoscia = (pionek == bialy) ? "Pionek Czarny" : "Pionek Biały"; 
$("body").html("<center><h3><ul style='list-style: none;'><li>"+nazwa_hosta+" - "+pionekHosta+"</li><li id='gosc'>"+nazwa_goscia+" - "+pionekGoscia+"</li></ul>\
<h1>Gra wygaśnie o: "+czas+"</h3></center>");

<?php if(@$_SESSION['kto'] == false){ ?>$("body").append("<center><input type='button' id='startButton' name='start' value='Start' disabled='disabled'></center>");
if($("body h3 ul #gosc").html().substr(0, 21) == "Oczekiwanie na gracza") $("#startButton").attr("disabled", "disabled");
if($("body h3 ul #gosc").html().substr(0, 21) != "Oczekiwanie na gracza") $("#startButton").removeAttr("disabled");
$("body").on("click", "input[name='start']", function(){
    pionek = (Math.random() >= 0.5) ? true : false;
    $.ajax({
        method: 'POST',
        url: 'prepareGame.php',
        data: {
            'pionek' : pionek,
            'stan' : 2
        }
    });
});

<?php } ?>

}
if(stan == 1) Lobby();

});
</script>
<body>
<div id="tura" style="color: white;"></div>
<table>


<?php
if(@$gameInfo['stan'] == 2){
    print "<script type='text/javascript'> $('table').removeClass('menu-table'); </script>";
for($i = 0; $i<100; $i++){
    if($i % 2 == 0) echo '<td id="'.$i.'" height="50px" width="50px" style="background-color: '.$color.'; border: 1px black solid;"></td>';
    else echo '<td id="'.$i.'"  height="50px" width="50px" style="background-color: '.$color2.'; border: 1px black solid;"></td>';
    if($i == $x){ echo "</tr>"; $x+=10; $zmiana = $color; $color = $color2; $color2 = $zmiana; }
}
}
if(!isset($_GET['id'])){
   $allGames = "SELECT * FROM gra WHERE gosc IS NULL;";
   $allGamesR = $dbh->prepare($allGames);
    $allGamesR->execute();
    if($allGamesR->rowCount() == 0) print "<center><h1>Brak Dostępnych Gier</h1></center>";
    else{
        print "<script type='text/javascript'> $('table').addClass('menu-table'); </script>";
    print "<tr><td class='menu'>ID</td><td class='menu'>Host</td><td class='menu'>Hasło</td><td class='menu'>Wygasa</td></tr>";
    while($allGamesE = $allGamesR->fetch(PDO::FETCH_ASSOC)):
if($allGamesE['prywatna'] == 0) $prywatna = "Niewymagane";
else if($allGamesE['prywatna'] == 1) $prywatna = "Wymagane";
echo "<tr><td class='menu'>".$allGamesE['id']."</td><td class='menu'>".$allGamesE['host']."</td><td class='menu'>".$prywatna."</td><td class='menu'>".$allGamesE['czas']."<td><button type='button' class='join' value='".$allGamesE['id']."'>Dołącz</button></td></tr>";
endwhile; } }?>
</table>



</table>
<style>
.menu-table{
    margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}
.menu{
    border: black solid 1px;
    height: 20;
    width: 100px;
    font-size: 25px;
    text-align: center; 
}
</style>
</body>
</html>