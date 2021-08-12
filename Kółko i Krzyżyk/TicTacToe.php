<?php
include('config.php');
session_start();
echo '<script src="http://code.jquery.com/jquery-3.5.1.min.js" type="text/javascript"></script>';
error_reporting(0);

function getIPAddress() {  
    //whether ip is from the share internet  
     if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
     }  
//whether ip is from the remote address  
    else{  
             $ip = $_SERVER['REMOTE_ADDR'];  
     }  
     return $ip;  
}  
$ip = getIPAddress();  

$_SESSION['ip'] = $ip;

if(isset($_POST['nazwa'])){

    $host = $_POST['nazwa'];
    $priv = $_POST['private'];
    $passwd = $_POST['passwd'];
$_SESSION['nazwa'] = $host;


    $lobby = "INSERT INTO `gra` (`host`, `ip_hosta`, `tura`, `prywatna`, `haslo`, `czas`) VALUES (:host, :ip, 0, $priv, :passwd, CURRENT_TIMESTAMP() + INTERVAL 60 minute);";
    $lobbyR = $dbh->prepare($lobby);
    $lobbyR->bindParam(":host", $host, PDO::PARAM_STR);
    $lobbyR->bindParam(":ip", $ip, PDO::PARAM_STR);
    $lobbyR->bindParam(":passwd", $passwd, PDO::PARAM_STR);
    $lobbyR->execute();

}

 $allGames = "SELECT * FROM `gra` WHERE gosc IS NULL;";
    $allGamesR = $dbh->prepare($allGames);
    $allGamesR->execute();

    $prywatna;

if(@$_GET['id']){
    $loadLobby = "SELECT * FROM gra WHERE id=".$_GET['id'].";";
    $loadLobbyR = $dbh->prepare($loadLobby);
    $loadLobbyR->execute();
    $lobbyInfo = $loadLobbyR->fetch(PDO::FETCH_ASSOC);

    $_SESSION['id'] = $lobbyInfo['id'];

    if($_SESSION['ip'] != ""){
        if($lobbyInfo['ip_goscia'] != null){
            if($_SESSION['ip'] != $lobbyInfo['ip_goscia'] && $_SESSION['ip'] != $lobbyInfo['ip_hosta']) echo "<script type='text/javascript'>alert('Ta gra jest pełna, poszukaj innej'); document.location='TicTacToe.php'</script>";
            else{
                if($lobbyInfo['ip_hosta'] == $_SESSION['ip']) $_SESSION['user'] = 0;
                else if($lobbyInfo['ip_goscia'] == $_SESSION['ip']) $_SESSION['user'] = 1;
            }
        }
        else if($lobbyInfo['ip_goscia'] == null){
            if($lobbyInfo['ip_hosta'] == $_SESSION['ip']) $_SESSION['user'] = 0;
            else if($lobbyInfo['ip_hosta'] != $_SESSION['ip']) $_SESSION['user'] = 1;


            if($_SESSION['user'] == 1){
                $dodajGoscia = "UPDATE gra SET ip_goscia='$ip' WHERE id=".$_GET['id'].";";
                $dodajGosciaR = $dbh->prepare($dodajGoscia);
                $dodajGosciaR->execute();
            }
        }
 
}

if($_SESSION['user'] == 1){
    if($lobbyInfo['prywatna'] == 1){
 echo "<script type='text/javascript'>if(prompt('Podaj hasło') != '".$lobbyInfo['haslo']."'){ alert('Podano nieprawidłowe hasło.'); location.reload(); } 
       else { 
        alert('Podano prawidłowe hasło'); 
        nick = prompt('Podaj nick'); 
        if(nick.length == 0){ alert('Musisz podać swój nick!'); location.reload(); }
        else $.ajax({ method: 'POST', url: 'TicTacToe.php?id=".$_GET['id']."', data: { 'nick' : nick }, success: function(){ alert('Powodzenia, '+nick+'!'); } }) };
        </script>";
}
else{
    echo "<script type='text/javascript'> 
     nick = prompt('Podaj nick'); 
     if(nick.length == 0){ alert('Musisz podać swój nick!'); location.reload(); }
     else $.ajax({ method: 'POST', url: 'TicTacToe.php?id=".$_GET['id']."', data: { 'nick' : nick }, success: function(){ alert('Powodzenia, '+nick+'!'); } }) };
     </script>";  
}
if(isset($_POST['nick'])){
        $dodajGoscia = "UPDATE gra SET `gosc`='".$_POST['nick']."', ip_goscia='$ip' WHERE id=".$_GET['id'].";";
        $dodajGosciaR = $dbh->prepare($dodajGoscia);
        $dodajGosciaR->execute();
}
}    
}     
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="StyleSheet" href="https://static.fontawesome.com/css/fontawesome-app.css" type="text/css"/>
        <link rel="StyleSheet" href=" https://pro.fontawesome.com/releases/v5.2.0/css/all.css" type="text/css"/>
    </head>

    <style>
.game{
    border: black solid 1px;
    height: 100px;
    width: 100px;
    font-size: 80px;
    text-align: center;
}
.game-table{
    margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}

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
.join{
    pointer-events: auto;
}
#easterEgg{
    margin: 0;
  position: absolute;
  right:0%;
  background-color: white;
  border: none;
}
.fa-question{
    color: #f0f0e9;
}
iframe{
    pointer-events: none;
}
    </style>

<script type="text/javascript">
$(document).ready(function(){
    var id = <?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo 0; ?>;
    var tura;
    var krzyzyk = '<i class="fas fa-times"></i>';
    var kolko = '<i class="far fa-circle"></i>';
    var figura;
    var jakaFigura;
    var figuraPrzeciwnika;
    var pole;
    var plansza = [];
    var ruch;
    var ruchPrzeciwnika;
var roznica;


    var warunki = [[0,3,6], [0,4,8], [1,4,7], [2,4,6], [2,5,8], [0,1,2], [3,4,5], [6,7,8]];
    var pola = [];
    var stanGry = <?php if(@$lobbyInfo['stan'] == "") echo 2; else echo $lobbyInfo['stan'] ?>;
 

    const indexOfAll = (arr, val) => arr.reduce((acc, el, i) => (el === val ? [...acc, i] : acc), []);




function Start(){
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

 $.ajax({
     method: "POST",
     url: "TicTacToe.php",
     data: {
        "nazwa" : nazwa,
        "private" : private,
        "passwd" : password
     },
     success: function(data){
        $("body").html(data);
     }
 });
});
}






$(".join").click(function(){
document.location = 'TicTacToe.php?id='+$(this).val();
});

<?php if(@$lobbyInfo['id']) {  ?> 
    
    
    function lobby(){
     tura = <?php echo $lobbyInfo['tura']; ?>;
    czas = '<?php echo $lobbyInfo['czas']; ?>';
    host = '<?php echo $lobbyInfo['host']; ?>';
    gosc = '<?php if($lobbyInfo['gosc'] == "") echo "Oczekiwanie na gracza"; else echo $lobbyInfo['gosc'] ?>';

var losuj;
var figury;



<?php 
$figury = "SELECT * FROM figura WHERE id_gry=".$_SESSION['id'].";";
$figuryR = $dbh->prepare($figury);
$figuryR->execute();
$figura = $figuryR->fetch(PDO::FETCH_ASSOC);
if($figura) { $_SESSION['figura'] = ($_SESSION['user'] == 0) ? $figura['figura_hosta'] : $figura['figura_goscia'];?> figury = true; <?php }  
?>


figura = '<?php if($figura){ echo ($_SESSION['user'] == 0) ? $figura['figura_hosta'] : $figura['figura_goscia']; } else echo ""; ?>';
figuraPrzeciwnika = '<?php if($figura){ echo ($_SESSION['user'] == 1) ? $figura['figura_hosta'] : $figura['figura_goscia']; } else  echo "";  ?>';

if(figura == "" && figuraPrzeciwnika == "") losuj = Math.floor(Math.random() * (1 + 1));



if(losuj == 0){ figura = krzyzyk; figuraPrzeciwnika = kolko; }
else if(losuj == 1){ figura = kolko; figuraPrzeciwnika = krzyzyk;  }

if(figury != true && figura != "" && figuraPrzeciwnika != ""){
    $.ajax({
        method: 'POST',
        url: 'rozgrywka.php',
        data: {
            'figura': figura,
            'figuraPrzeciwnika' : figuraPrzeciwnika
        },
        success: function(){
            location.reload();
        }
    });
}

$("body").html("<center><h3><ul style='list-style: none;'><li>"+host+" - "+figura+"</li><li id='gosc'>"+gosc+" - "+figuraPrzeciwnika+"</li></ul>\
<h1>Gra wygaśnie o: "+czas+"</h3></center>");

<?php if($_SESSION['user'] == 0){ ?>$("body").append("<center><input type='button' id='startButton' name='start' value='Start' disabled='disabled'></center>");
if($("body h3 ul #gosc").html().substr(0, 21) == "Oczekiwanie na gracza") $("#startButton").attr("disabled", "disabled");
if($("body h3 ul #gosc").html().substr(0, 21) != "Oczekiwanie na gracza") $("#startButton").removeAttr("disabled");
$("body").on("click", "input[name='start']", function(){
    generujPlansze();
});
<?php } ?>
if(stanGry == 1){ generujPlansze();}
}
    
    
     lobby(); <?php } ?>


    function tury(){
  
       
     
    if($("h2").html().indexOf("Tura Hosta") >= 0 || $("h2").html().indexOf("Tura Gościa") >= 0){
        <?php if($_SESSION['user'] == 0){ ?>

        if(tura == 0) {  $("h2").html($("h2").html().replace("Tura Gościa","Tura Hosta")); $("table").css("pointer-events", "auto"); }
        if(tura == 1) {  $("h2").html($("h2").html().replace("Tura Hosta","Tura Gościa")); $("table").css("pointer-events", "none"); }

        <?php } if($_SESSION['user'] == 1) { ?>

        if(tura == 0) {  $("h2").html($("h2").html().replace("Tura Gościa","Tura Hosta")); $("table").css("pointer-events", "none"); }
        if(tura == 1) {  $("h2").html($("h2").html().replace("Tura Hosta","Tura Gościa")); $("table").css("pointer-events", "auto"); }
        
       <?php } ?>
    }
    else{
        <?php if($_SESSION['user'] == 0){ ?>
    if(tura == 0) { $("h2").append("</br>Tura Hosta"); $("table").css("pointer-events", "auto"); }
    if(tura == 1) { $("h2").append("</br>Tura Gościa"); $("table").css("pointer-events", "none"); }

    <?php } if($_SESSION['user'] == 1) { ?>
        if(tura == 0) { $("h2").append("</br>Tura Hosta"); $("table").css("pointer-events", "none"); }
    if(tura == 1) { $("h2").append("</br>Tura Gościa"); $("table").css("pointer-events", "auto"); }
    <?php } ?>
    }
 
   }

   if(figura == kolko) jakaFigura = 1;
    if(figura == krzyzyk) jakaFigura = 0;

  
    $("#easterEgg").click(function(){
        console.log("??");
       if(prompt("Jak nazywa się słynny pierwszy 'program' na początku nauki programowania? (z uwzględnieniem dużych liter i znaków)") == "Hello, World!") $("body").append('<center><iframe src="https://giphy.com/embed/Nx0rz3jtxtEre" width="480" height="240"></iframe></center>'); else alert("Niestety podałeś niepoprawną odpowiedź :(");
    });


    function generujPlansze(){

        $("body").html("Czekaj");
setInterval(function(){
$.ajax({
    method: 'POST',
    url: 'plansza.php',
  

    success: function(data){
        data = data.trim();
        if(data.slice(data.length - 1, data.length) != tura) { tura = data.slice(data.length - 1, data.length); tury(); }
        data = data.substring(0, data.length - 1);
  
if($("body").html().length - data.length != roznica) { $("body").html(data); tury(); rozgrywka(); }
roznica = $("body").html().length - data.length;


    }
})

}, 1500);

 
   
 

 


$.ajax({
    method: 'POST',
    url: "rozgrywka.php",
    data:{
        'stan' : 1
    },
    success: function(){
        gra();
    }
})
    }

    

    function rozgrywka(){

        plansza = [];

    $("td").each(function(f){
       
        for(x = 0; x<=8; x++){
    if(f == x) {
        plansza.push($(this).html());
        if($(this).html() != ""){  
$(this).css("pointer-events", "none");

        }
        }
    }
    });
                ruch = indexOfAll(plansza, figura);
                ruchPrzeciwnika = indexOfAll(plansza, figuraPrzeciwnika);

               

                for(i = 0; i<=7; i++){
                 
                 if(warunki[i].every(r => ruch.includes(r))){
                    setTimeout(function(){    
                    
                        $.ajax({
                            method: 'POST',
                            url: 'rozgrywka.php',
                            data: {
                                 'pole1' : 2,
                                 'pole2' : 2,
                                 'pole3' : 2,
                                 'pole4' : 2,
                                 'pole5' : 2,
                                 'pole6' : 2,
                                 'pole7' : 2,
                                 'pole8' : 2,
                                 'pole9' : 2
                                }
                        });        
                        alert("Wygrałeś!");
                    }, 10); break;}
                    
                 else if(warunki[i].every(r => ruchPrzeciwnika.includes(r))){
                    setTimeout(function(){    
                    
                        $.ajax({
                            method: 'POST',
                            url: 'rozgrywka.php',
                            data: {
                                 'pole1' : 2,
                                 'pole2' : 2,
                                 'pole3' : 2,
                                 'pole4' : 2,
                                 'pole5' : 2,
                                 'pole6' : 2,
                                 'pole7' : 2,
                                 'pole8' : 2,
                                 'pole9' : 2
                                }
                        }); 
                        alert("Przegrałeś!");  
                    }, 10); break;}
                 
                 else if(ruch.length == 5 && ruchPrzeciwnika.length == 4 || ruch.length == 4 && ruchPrzeciwnika.length == 5){
                    setTimeout(function(){    
                    
                        $.ajax({
                            method: 'POST',
                            url: 'rozgrywka.php',
                            data: {
                                 'pole1' : 2,
                                 'pole2' : 2,
                                 'pole3' : 2,
                                 'pole4' : 2,
                                 'pole5' : 2,
                                 'pole6' : 2,
                                 'pole7' : 2,
                                 'pole8' : 2,
                                 'pole9' : 2
                                }
                        }); 
                        alert("Remis!");

                    }, 10);  break;  }
              
                }             
         

  

}
rozgrywka();

function gra(){

$("body").on('click', 'table tbody tr td', function(){

    $(this).html(figura);
if($(this).html() == figura) pola[$(this).attr('id')-1] = jakaFigura;

    rozgrywka();


for(var k = 0; k<9; k++){

    if(plansza[k] == krzyzyk) pola[k] = 0;

    if(plansza[k] == kolko) pola[k] = 1;

    if(plansza[k] == "") pola[k] = 2;
}







$.ajax({
method: 'POST',
url: 'rozgrywka.php',
async: false,
data: {
    'pole1' : pola[0],
    'pole2' : pola[1],
    'pole3' : pola[2],
    'pole4' : pola[3],
    'pole5' : pola[4],
    'pole6' : pola[5],
    'pole7' : pola[6],
    'pole8' : pola[7],
    'pole9' : pola[8]
},
success: function(data){
    tura = data;
    tury();
   
}
});



rozgrywka();

});


}

$("#StartFunction").click(function(){
Start();
});

});
</script>

    <body>
    <table class="menu-table">
    <tr><td class='menu'>ID</td><td class='menu'>Host</td><td class='menu'>Hasło</td><td class='menu'>Wygasa</td></tr>
    <?php while($allGamesE = $allGamesR->fetch(PDO::FETCH_ASSOC)):
if($allGamesE['prywatna'] == 0) $prywatna = "Niewymagane";
else if($allGamesE['prywatna'] == 1) $prywatna = "Wymagane";
echo "<tr><td class='menu'>".$allGamesE['id']."</td><td class='menu'>".$allGamesE['host']."</td><td class='menu'>".$prywatna."</td><td class='menu'>".$allGamesE['czas']."<td><button type='button' class='join' value='".$allGamesE['id']."'>Dołącz</button></td></tr>";

endwhile; ?>
</table>
<button type="button" id="StartFunction">Stwórz poczekalnię</button>
<button type="button"><a href="TicTacToe.html">Gra przeciwko komputerowi</a></button>
<button type="button" id="easterEgg"><i class="fas fa-question"></i></button>
    </body>
</html>
