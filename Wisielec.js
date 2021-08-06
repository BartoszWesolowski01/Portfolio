$(document).ready(function () {
  $("#start").click(function () {
    $(this).toggle();
    var slowa = [
      {
        "kategoria": "zwierzęta",
        "slowo": "Kot",
      },
      {
        "kategoria": "zwierzęta",
        "slowo": "Ratel",

      },
      {
        "kategoria": "samochody",
        "slowo": "Toyota Supra",
      },
      {
        "kategoria": "samochody",
        "slowo": "Nissan Skyline",
      },
      {
        "kategoria": "jedzenie",
        "slowo": "Pizza",
      },
      {
        "kategoria": "jedzenie",
        "slowo": "Pierogi",
      },
      {
        "kategoria": "Języki programowania",
        "slowo": "PHP",
      },
      {
        "kategoria": "Języki programowania",
        "slowo": "Python",
      },
    ];

    var keys = Object.keys(slowa);
    var wisielec = keys[Math.floor(Math.random() * keys.length)];
    var spacja = slowa[wisielec].slowo.indexOf(' ');

    $("#kategoria").html(slowa[wisielec].kategoria);

    for (y = 0; y < slowa[wisielec].slowo.length; y++) {
      $("<td id='" + y + "' class='zgadniete'>_</td>").appendTo("#odp");
    }
    $("#" + spacja).html(" ");
    var prob = 12;
    var a = ['a', 'ą', 'b', 'c', 'ć', 'd', 'e', 'ę', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'ł', 'm', 'n', 'ń', 'o', 'ó', 'p', 'q', 'r', 's', 'ś', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ż', 'ź'];
    for (i = 0; i <= 34; i++) {
      $("<td id='" + a[i] + "' class='litery'>" + a[i] + "</td>").appendTo("#alfabet");
    }
    $(".litery").click(function () {
      var litera = $(this).attr("id");
      litera = litera.toString();
      var wylosowaneSlowo = slowa[wisielec].slowo;
      wylosowaneSlowo = wylosowaneSlowo.toLowerCase();
      wylosowaneSlowo = wylosowaneSlowo.toString();
      var id_litery = [];



      if (wylosowaneSlowo.indexOf(litera) > -1) {
        for (o = 0; o < wylosowaneSlowo.length; o++) {
          if (wylosowaneSlowo[o] == litera) {
            id_litery.push(o);
            for (e = 0; e < id_litery.length; e++) {
              $("#" + id_litery[e]).html(litera);
              $("#" + litera).css("background-color", "green");
              $("#" + litera).css("pointer-events", "none");
              if ($(".zgadniete").text() == wylosowaneSlowo) {
                setTimeout(function () {
                  alert("Gratulacje, wygrałeś!");
                  location.reload();
                }, 50);
              }
            }
          }
        }
      }
      else if (wylosowaneSlowo.indexOf(litera) == -1) {
        prob--;
        $("#ile").html("Prób: " + prob);
        $("#" + litera).css("background-color", "red");
        $("#" + litera).css("pointer-events", "none");
        if (prob == 0) {
          setTimeout(function () {
            alert("Przegrałeś.");
            location.reload();
          }, 50);
        }
      }


    }

    );
  });

});
