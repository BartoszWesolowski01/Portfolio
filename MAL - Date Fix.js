// ==UserScript==
// @name         MAL Date Fix
// @namespace    Fix your dates on MAL
// @version      1.3
// @description  Script that sets your Start/Finish Dates on MAL
// @author       Wesołowski Bartosz
// @match        https://anime.plus/*/history,anime
// @match        https://myanimelist.net/ownlist/*
// @grant        GM_xmlhttpRequest
// @grant        GM_setValue
// @grant        GM_getValue
// @grant        GM_deleteValue
// @grant        GM_listValues
// @require      http://code.jquery.com/jquery-3.5.1.min.js
// ==/UserScript==


// Variables

const $ = window.jQuery;

var id = [];
var startDay = [];
var startMonth = [];
var startYear = [];
var endDay = [];
var endMonth = [];
var endYear = [];

var csrf_token;

var how_much, startDate, endDate;

var aeps = [], astatus = [], status = [], num_watched_episodes = [], score = [], tags = [], priority = [], storage_type = [], storage_value = [], num_watched_times = [], rewatch_value = [], comments = [], is_asked_to_discuss = [], sns_post_type = [], is_rewatching = [], submitIt = [];


if (GM_getValue('sns_post_type') != undefined) {
  storage_type = GM_getValue('storage_type');
  storage_value = GM_getValue('storage_value');
  is_rewatching = GM_getValue('is_rewatching');
  aeps = GM_getValue('aeps');
  status = GM_getValue('status');
  astatus = GM_getValue('astatus');
  num_watched_times = GM_getValue('num_watched_times');
  num_watched_episodes = GM_getValue('num_watched_episodes');
  comments = GM_getValue('comments');
  sns_post_type = GM_getValue('sns_post_type');
  score = GM_getValue('score');
  rewatch_value = GM_getValue('rewatch_value');
  tags = GM_getValue('tags');
  priority = GM_getValue('priority');
  is_asked_to_discuss = GM_getValue('is_asked_to_discuss');
}

if (GM_getValue('endYear') != undefined) {
  startDay = GM_getValue('startDay');
  startMonth = GM_getValue('startMonth');
  startYear = GM_getValue('startYear');
  endDay = GM_getValue('endDay');
  endMonth = GM_getValue('endMonth');
  endYear = GM_getValue('endYear');
}



$(window).ready(async () => {

  // Function to return requests in orginal order, without making them sync

  function SendGetRequest(url) {
    return new Promise((resolve, reject) => {
      GM_xmlhttpRequest({
        method: "GET",
        url: url,
        onload: function (response) {
          resolve(response.responseText);
        },
        onerror: function (error) {
          reject(error);
        }
      });
    });
  }
  if (window.location.pathname.split('/')[2] === 'history,anime') {

    // Checks the URL, then proceeds to gather ID's of titles that got missing dates

    $('a.entries-trigger')[0].click();

    how_much = parseInt($('a.entries-trigger').text());

    alert("Start");

    $('#month-unknown ul li a').each(function () {

      id.push(parseInt($(this).attr('href').slice(30)));
    });

    if (id.length == how_much) {
      GM_setValue('id', id);

      // After getting all of the ID's, redirects You to MAL, to avoid CROSS-ORIGIN problems
      document.location = "https://myanimelist.net/ownlist/anime/" + id[0] + "/edit";
    }

  }




  if (window.location.pathname.split('/')[1] === 'ownlist') {

    if ($(".g-recaptcha").length > 0) {
      $(".g-recaptcha").click();
    }
    else {

      id = GM_getValue('id');
      if (id == null) alert("No Data, please Start from the Beginning");

      $("body").html("<label for='p1'>Fetching information</label></br><progress id='p1' value='0' max='" + id.length + "'></progress></br><label for='p2'>Fetching Dates</label></br><progress id='p2' value='0' max='" + id.length + "'></progress></br><label for='p3'>Sending Update Requests</label></br><progress id='p3' value='0' max='" + id.length + "'></progress>");

      csrf_token = $("meta[name='csrf_token']").attr('content');

      // After redirecting fetches data saved in the TamperMonkey, then gets csrf_token to allow sending update requests, and starts requesting current data of all the titles using previously obtained ID's

      // in case when you got temporaly block to avoid request spam

      var i;
      if (GM_getValue('i') == null) i = 0;
      else i = GM_getValue('i');

      var f;
      if (GM_getValue('f') == null) f = 0;
      else { f = GM_getValue('f'); i = id.length - 1; }

      var e;
      if (GM_getValue('e') == null) e = 0;
      else { e = GM_getValue('e'); i = id.length - 1; f = id.length - 1; }



      for (i; i < id.length; i++) {
        $("#p1").attr("value", i + 1);

        try {
          const response = await SendGetRequest("https://myanimelist.net/ownlist/anime/" + id[i] + "/edit");
          $(response).find("#main-form :input").each(function (x) {

            // Gets all the current data except dates, by checking values of the inputs

            if (x == 1)
              aeps.push($(this).val());
            if (x == 2)
              astatus.push($(this).val());
            if (x == 3)
              status.push($(this).val());
            if (x == 4)
              is_rewatching.push($(this).val());
            if (x == 5)
              num_watched_episodes.push($(this).val());
            if (x == 6)
              score.push($(this).val());
            if (x == 15)
              tags.push($(this).val());
            if (x == 16)
              priority.push($(this).val());
            if (x == 17)
              storage_type.push($(this).val());
            if (x == 18)
              storage_value.push($(this).val());
            if (x == 19)
              num_watched_times.push($(this).val());
            if (x == 20)
              rewatch_value.push($(this).val());
            if (x == 21)
              comments.push($(this).val());
            if (x == 22)
              is_asked_to_discuss.push($(this).val());
            if (x == 23)
              sns_post_type.push($(this).val());

          });

        } catch (error) {
          if (error.status != null) {
            GM_setValue('i', i); GM_setValue('storage_type', storage_type); GM_setValue('num_watched_episodes', num_watched_episodes); GM_setValue('sns_post_type', sns_post_type);
            GM_setValue('aeps', aeps); GM_setValue('storage_value', storage_value); GM_setValue('score', score);
            GM_setValue('astatus', astatus); GM_setValue('num_watched_times', num_watched_times); GM_setValue('tags', tags);
            GM_setValue('status', status); GM_setValue('rewatch_value', rewatch_value); GM_setValue('priority', priority);
            GM_setValue('is_rewatching', is_rewatching); GM_setValue('comments', comments); GM_setValue('is_asked_to_discuss', is_asked_to_discuss);

            $("body").html("Request failed with error code", error.status, ". Message is ", error.responseText);
            break;
          }
        }

      }

      if (aeps.length == id.length) {

        // After obtaining current data, it proceeds to get Missing Dates

        for (f; f < id.length; f++) {

          $("#p2").attr("value", f + 1);

          try {
            const response = await SendGetRequest("https://myanimelist.net/ajaxtb.php?keepThis=true&detailedaid=" + id[f] + "&TB_iframe=true&height=420&width=390");
            var length = $(response).find('.spaceit_pad').length;
            $($(response).find('.spaceit_pad').get().reverse()).each(function (x) {

              // Takes first record as the start date, and first one (to get first watch dates, not the rewatch ones) that got the number of the last ep as the Finish Date

              if (x == 0) {
                startDate = $(this).html().match(/\d{2}([\/.-])\d{2}\1\d{4}/g);
                startDate = startDate.toString();

                startDay.push(parseInt(startDate.split('/')[1], 10));
                startMonth.push(parseInt(startDate.split('/')[0], 10));
                startYear.push(parseInt(startDate.split('/')[2], 10));
              }

              if (x < length && $(this).html().indexOf("Ep " + aeps[f]) == 0) {
                endDate = $(this).html().match(/\d{2}([\/.-])\d{2}\1\d{4}/g);
                endDate = endDate.toString();

                endDay.push(parseInt(endDate.split('/')[1], 10));
                endMonth.push(parseInt(endDate.split('/')[0], 10));
                endYear.push(parseInt(endDate.split('/')[2], 10));

                return false;
              }
              x++


            });

          } catch (error) {

            if (error.status != null) {

              $("body").html("Request failed with error code", error.status, ". Message is - ", error.responseText);
              GM_setValue('f', f);
              GM_setValue('storage_type', storage_type); GM_setValue('num_watched_episodes', num_watched_episodes); GM_setValue('sns_post_type', sns_post_type);
              GM_setValue('aeps', aeps); GM_setValue('storage_value', storage_value); GM_setValue('score', score);
              GM_setValue('astatus', astatus); GM_setValue('num_watched_times', num_watched_times); GM_setValue('tags', tags);
              GM_setValue('status', status); GM_setValue('rewatch_value', rewatch_value); GM_setValue('priority', priority);
              GM_setValue('is_rewatching', is_rewatching); GM_setValue('comments', comments); GM_setValue('is_asked_to_discuss', is_asked_to_discuss);
              GM_setValue('startDay', startDay);
              GM_setValue('startMonth', startMonth);
              GM_setValue('startYear', startYear);
              GM_setValue('endDay', endDay);
              GM_setValue('endMonth', endMonth);
              GM_setValue('endYear', endYear);
              break;
            }
          }

        }
      }


      if (endYear.length == id.length) {


        // Last Stage - just sends update requests

        for (e; e < id.length; e++) {



          $.ajax({
            method: 'POST',
            url: 'https://myanimelist.net/ownlist/anime/' + id[e] + '/edit',
            data: {
              'anime_id': id[e],
              'aeps': aeps[e],
              'astatus': astatus[e],
              'add_anime[status]': status[e],
              'add_anime[num_watched_episodes]': num_watched_episodes[e],
              'add_anime[score]': score[e],
              'add_anime[start_date][month]': startMonth[e],
              'add_anime[start_date][day]': startDay[e],
              'add_anime[start_date][year]': startYear[e],
              'add_anime[finish_date][month]': endMonth[e],
              'add_anime[finish_date][day]': endDay[e],
              'add_anime[finish_date][year]': endYear[e],
              'add_anime[tags]': tags[e],
              'add_anime[priority]': priority[e],
              'add_anime[storage_type]': storage_type[e],
              'add_anime[storage_value]': storage_value[e],
              'add_anime[num_watched_times]': num_watched_times[e],
              'add_anime[rewatch_value]': rewatch_value[e],
              'add_anime[comments]': comments[e],
              'add_anime[is_asked_to_discuss]': is_asked_to_discuss[e],
              'add_anime[sns_post_type]': sns_post_type[e],
              'submitIt': 0,
              'csrf_token': csrf_token
            },
            complete: function () {
              $("#p3").attr("value", e + 1);
              if (e == id.length) { setTimeout(async function () { $("body").html("<h1>The End</h1>"); let keys = await GM_listValues(); for (let key of keys) { GM_deleteValue(key); } }, 500); }
            },
            error: function () {
              GM_setValue('e', e);
              GM_setValue('storage_type', storage_type); GM_setValue('num_watched_episodes', num_watched_episodes); GM_setValue('sns_post_type', sns_post_type);
              GM_setValue('aeps', aeps); GM_setValue('storage_value', storage_value); GM_setValue('score', score);
              GM_setValue('astatus', astatus); GM_setValue('num_watched_times', num_watched_times); GM_setValue('tags', tags);
              GM_setValue('status', status); GM_setValue('rewatch_value', rewatch_value); GM_setValue('priority', priority);
              GM_setValue('is_rewatching', is_rewatching); GM_setValue('comments', comments); GM_setValue('is_asked_to_discuss', is_asked_to_discuss);
              GM_setValue('startDay', startDay);
              GM_setValue('startMonth', startMonth);
              GM_setValue('startYear', startYear);
              GM_setValue('endDay', endDay);
              GM_setValue('endMonth', endMonth);
              GM_setValue('endYear', endYear);
              setTimeout(function () { location.reload(); }, 500);
            }
          });

        }
      }

    }
  }
});