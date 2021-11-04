// ==UserScript==
// @name         Anime Songs - AniList Player
// @namespace    Openings and Endings Player
// @version      1.2.6
// @description  This Script allows You to play Openings and Endings directly on AniList
// @author       Wesołowski Bartosz
// @match        https://anilist.co/*
// @icon         data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==
// @grant        GM_xmlhttpRequest
// @grant        GM_setValue
// @grant        GM_getValue
// @require      http://code.jquery.com/jquery-3.5.1.min.js
 
// ==/UserScript==
 
const $ = window.jQuery;
 
var result;
 
var openings, endings;
 
var url = "https://graphql.anilist.co";
 
var AnimeID;
 
var AniID = {};
 
function loadVideos(AnimeID) {
  var Song = 0;
 
  (function init() {
    openings = document.getElementsByClassName("openings");
    endings = document.getElementsByClassName("endings");
    if (openings.length > 0 || endings.length > 0) {
      GM_xmlhttpRequest({
        method: "GET",
        url:
          "https://staging.animethemes.moe/api/anime?filter[Anime][id]=" +
          AnimeID +
          "&include=animethemes.animethemeentries.videos,animethemes.song",
        data: AnimeID,
        headers: { "Content-Type": "application/json" },
        onload: function (response) {
          result = JSON.parse(response.responseText);
          openings = openings.length > 0 ? openings : endings;
          $(openings).append(
            "<video style='display: none' id='Player' width='500px' height='300' src='' allowfullscreen controls autoplay/>"
          );
 
          var number = 0;
 
          start_position: while (true) {
            number++;
 
            if (
              $("div.openings, div.endings").children(".tag").html().length > 0
            )
              break;
            else continue start_position;
          }
          for (Song; Song < result.anime[0].animethemes.length; Song++) {
            $("div.openings, div.endings")
              .children(".tag")
              .filter(function () {
                var currSong = $(this);
 
                if (
                  $(currSong)
                    .text()
                    .split(" by ")[0]
                    .replace(/\(([^)]+)\)/, "")
                    .replace(/[“"”]+/g, "")
                    .replace(/\#\d*:/g, "")
                    .replace(/\d*\:/g, "")
                    .replace(/[^\w\s]/gi, " ")
                    .trim() ==
                  result.anime[0].animethemes[Song].song.title
                    .replace(/\(([^)]+)\)/, "")
                    .replace(/\#\d*:/g, "")
                    .replace(/[^\w\s]/gi, " ")
                    .trim()
                ) {
                  $(currSong).html(
                    "<a href='https://animethemes.moe/video/" +
                      result.anime[0].animethemes[Song].animethemeentries[0]
                        .videos[0].basename +
                      "'>" +
                      $(currSong).text() +
                      "</a>"
                  );
                }
              });
          }
        },
        onerror: function (error) {
          console.log(error);
        },
      });
 
      $(".tag").click(function (e) {
        e.preventDefault();
 
        if ($(this).children().length == 0) {
          alert("Sadly, this OP/ED is Missing or there's a bug in the Script (Check Instruction)");
        } else if (
          $("body #Player").css("display") == "none" ||
          $(this).children().attr("href") != $("body #Player").attr("src")
        ) {
          $("body #Player").css("display", "block");
 
          $("body #Player").attr("src", $(this).children().attr("href"));
        } else if (
          $(this).children().attr("href") == $("body #Player").attr("src")
        ) {
          $("body #Player").css("display", "none");
          $("body #Player").attr("src", "");
        }
      });
    } else {
      setTimeout(init, 0);
    }
  })();
}
 
function getMoeID(AID) {
  GM_xmlhttpRequest({
    method: "GET",
    url:
      "https://staging.animethemes.moe/api/resource?filter[external_id]=" +
      AID +
      "&include=anime",
    headers: { "Content-Type": "application/json" },
    onload: function (response) {
      result = JSON.parse(response.responseText);
      loadVideos(result.resources[0].anime[0].id);
    },
    onerror: function (error) {
      console.log(error);
    },
  });
}
 
function getAID() {
  var query = `
query ($id: Int) {
  Media (id: $id, type: ANIME) {
 idMal
  }
}
`;
 
  var options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      query: query,
      variables: AniID,
    }),
  };
 
  fetch(url, options).then(handleResponse).then(handleData).catch(handleError);
 
  function handleResponse(response) {
    return response.json().then(function (json) {
      return response.ok ? json : Promise.reject(json);
    });
  }
 
  function handleData(data) {
    AnimeID = data.data.Media.idMal;
    getMoeID(AnimeID);
  }
 
  function handleError(error) {
    alert("Error, check console");
    console.error(error);
  }
}
 
(function init() {
  openings = document.getElementsByClassName("openings");
  endings = document.getElementsByClassName("endings");
  if (openings.length > 0 || endings.length > 0) {
    AniID = { id: parseInt(window.location.pathname.split("/")[2]) };
    getAID();
 
    let mList = document.querySelector(".content h1"),
      options = {
        characterData: true,
        childList: true,
        subtree: true,
      },
      observer = new MutationObserver(mCallback);
 
    function mCallback(mutations) {
      if (mutations.length == 1) {
        AniID = { id: parseInt(window.location.pathname.split("/")[2]) };
        getAID();
      }
    }
 
    observer.observe(mList, options);
  } else {
    setTimeout(init, 0);
  }
})();
 
var oldURL;
 
(function () {
  var pushState = history.pushState;
  var replaceState = history.replaceState;
 
  history.pushState = function () {
    pushState.apply(history, arguments);
    window.dispatchEvent(new Event("pushstate"));
    window.dispatchEvent(new Event("locationchange"));
  };
 
  history.replaceState = function () {
    replaceState.apply(history, arguments);
    window.dispatchEvent(new Event("replacestate"));
    window.dispatchEvent(new Event("locationchange"));
  };
 
  window.addEventListener("popstate", function () {
    window.dispatchEvent(new Event("locationchange"));
  });
})();
 
$(window).on("load", function () {
  GM_setValue("oldURL", $(location).attr("pathname").split("/")[1]);
});
 
window.addEventListener("locationchange", function () {
  oldURL = GM_getValue("oldURL");
 
  if (
    oldURL != "anime" &&
    $(location).attr("pathname").split("/")[1] == "anime"
  ) {
    GM_setValue("oldURL", $(location).attr("pathname").split("/")[1]);
    location.reload();
  } else GM_setValue("oldURL", $(location).attr("pathname").split("/")[1]);
});