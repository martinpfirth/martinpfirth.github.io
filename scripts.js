$(document).ready(function() {
    var searchDelay;
    // var colours = ["f94144","f3722c","f8961e","f9844a","f9c74f","90be6d","43aa8b","4d908e","577590","277da1"];
    var colours = ["277da1","f8961e","43aa8b","f94144","577590","f9844a","f9c74f","4d908e","f3722c","90be6d"]


    //Type show
    $("#show_name").keyup(function(){
        $("#show_group").addClass("searching");
        clearTimeout(searchDelay);
        var entered_text = $(this).val();
        if(entered_text.length>3){
            searchDelay = setTimeout(function(){ showSearch(entered_text) }, 1000);
        }
    });

    $("#show_name").blur(function(){
        //need delay so click registers
        setTimeout(function() {
            $("#show_group").removeClass("searching searched");
        }, 200);
    });

    $("#show_name").focus(function(){
        if($("#show_name").val().length>3){
            $("#show_group").addClass("searched");
        }
    });

    function showSearch(search_text){
        console.log('showing search');
        var encoded_name = encodeURIComponent(search_text).replace(/%20/g, "+");;
        console.log(encoded_name);

        $.getJSON('http://api.tvmaze.com/search/shows?q=' + encoded_name, function(data) {
            var resultCount = Math.min(data.length, 5);
            var results = "";
            for (i = 0; i < resultCount; i++) { 
                var result_item = `<li class="show" data-id="${data[i].show.id}">${data[i].show.name}</li>`;
                results += result_item;
            }
            $("#show_suggestions").html(results);

            $("#show_suggestions li").click(function(){
                var show_id = $(this).data("id");
                var show_name = $(this).html();
                $("#show_group").removeClass("searching searched");
                chooseShow(show_id, show_name);
            });

            $("#show_group").removeClass("searching");
            $("#show_group").addClass("searched");
        });
    }

    var episodes = [];

    function chooseShow(show_id, show_name){
        console.log("chosen show " + show_id);
        $("#show_name").val(show_name);
        //UI change
        $("#show_group").removeClass("searched searching");
        $(".show").remove();
        $.getJSON('http://api.tvmaze.com/shows/' + show_id + '/episodes', function(data) {
            var episode_count = data.length;
            var season_count = data[episode_count-1].season;

            episodes = data;

            $("#start_season, #end_season").attr("max", season_count);
            $("#end_season").val(season_count);
        });

        $("#frequency_num").focus();
    }

    $("#range_name").change(function(){
        var range = $(this).val();
        if(range == "only"){
            $("#season_range_group").show();
        } else {
            $("#season_range_group").hide();
        }
        //season_range_group
    });

    function createEvents(){
        //loop episodes, each one create event

        //10mins between episodes
        //first episode at 10am

            var episode_count = episodes.length;
            var season_count = episodes[episode_count-1].season;
            var total_runtime = 0;
            

            //January is 0. December is 11.
            //var key_date = new Date(2020, 08, 29, 10, 33, 30, 0);

            var key_date = $('#key_date').datepicker('getDate');

            var watch_date = key_date;

            var episode_events = []; 
            var day_count = 0;

            var plotForwards = true;
            

            if($("#start_or_end").val() == "starting"){
                plotForwards = true;
            } else if($("#start_or_end").val() == "finishing"){
                plotForwards = false;
            } else {
                alert("don't know whether to go forwards or backwards bro")
            }

            

            for (i = 0; i < episodes.length; i++) { 

                //Using a start date and plotting forwards
                //or using and end date and going backwards
                if(plotForwards == true){
                    var episode = episodes[i];
                } else {
                    //start at the last episode
                    var episode = episodes[episode_count - i - 1];
                }

                //If episode within watch range
                if($("#range_name").val() == "only"){
                    var start_season = $("#start_season").val();
                    var end_season = $("#end_season").val();
                    
                    if(episode.season < start_season || episode.season > end_season){
                        continue; //out of season range
                    }
                }
            

                var episode_event = {
                    title : formatTitle(episode.season, episode.number) + ": " + episode.name,
                    start : watch_date.toISOString(),
                    duration: episode.runtime,
                    backgroundColor: colours[episode.season],
                    url: episode.url
                }

                // console.log(episode_event.title + ": " + episode_event.duration);

                //Per Day
                if($("#frequency_name").val() == "day"){
                    var frequency = parseInt($("#frequency_num").val());
                    if(plotForwards == true){
                        dateDirection = 1;
                    } else {
                        dateDirection = -1;
                    }
                    if(frequency == day_count){
                        day_count = 0;
                        watch_date.setDate(watch_date.getDate() + dateDirection); //increment day
                        episode_event.start = watch_date.toISOString();
                    }
                    episode_events.push(episode_event);
                    total_runtime += episode_event.duration;
                }

                //Per Week
                if($("#frequency_name").val() == "week"){            
                    if(plotForwards == true){
                        dateDirection = 1;
                    } else {
                        dateDirection = -1;
                    }
                    
                    var frequency = parseInt($("#frequency_num").val());
                    var week = getEpisodeDistribution(frequency);
                    var day = watch_date.getDay();
                    
                    //episode today
                    if(day_count >= week[day]){
                        day_count = 0;
                        watch_date.setDate(watch_date.getDate() + dateDirection); //increment day
                        episode_event.start = watch_date.toISOString();
                    }
                    if(week[day] != 0){
                        episode_events.push(episode_event);
                        total_runtime += episode_event.duration;
                    } 
                }

                //Per Weekday
                if($("#frequency_name").val() == "weekday"){
                    var frequency = parseInt($("#frequency_num").val());
                    if(frequency == day_count){
                        //new day
                        day_count = 0;
                        if(plotForwards == true){
                            if(watch_date.getDay() == 5){
                                watch_date.setDate(watch_date.getDate() + 3); //increment weekend
                            } else {
                                watch_date.setDate(watch_date.getDate() + 1); //increment day
                            }
                        } else {
                            if(watch_date.getDay() == 1){
                                watch_date.setDate(watch_date.getDate() - 3); //increment weekend
                            } else {
                                watch_date.setDate(watch_date.getDate() - 1); //increment day
                            }
                        }

                        episode_event.start = watch_date.toISOString();
                    }
                    
                    episode_events.push(episode_event);
                    total_runtime += episode_event.duration;
                }
                
                //Weekend Day
                if($("#frequency_name").val() == "weekendday"){
                    var frequency = parseInt($("#frequency_num").val());
                    if(frequency == day_count || i == 0){
                        //new day
                        day_count = 0;

                        if(plotForwards == true){
                            if(watch_date.getDay() == 6){
                                watch_date.setDate(watch_date.getDate() + 1); //increment day
                            } else {
                                var increment = 6 - watch_date.getDay();
                                watch_date.setDate(watch_date.getDate() + increment); //increment weekend
                            }
                        } else {
                            if(watch_date.getDay() == 0){
                                watch_date.setDate(watch_date.getDate() - 1); //increment day
                            } else {
                                var increment = 0 - watch_date.getDay();
                                watch_date.setDate(watch_date.getDate() + increment); //increment weekend
                            }
                        }

                        episode_event.start = watch_date.toISOString();
                    }
                    episode_events.push(episode_event);
                    total_runtime += episode_event.duration;
                }

                day_count++;
            }

            
            var initial = episode_events[episode_events.length-1].start;


            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: episode_events,
                themeSystem: 'default',
                displayEventTime: false,
                initialDate: initial,
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate    
                    if (info.event.url) {
                        window.open(info.event.url);
                    }
                }
            });

            function formatDate(date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2) 
                    month = '0' + month;
                if (day.length < 2) 
                    day = '0' + day;

                return [day, month, year].join('/');
            }

            calendar.render();

            var info = "Number of episodes: " + episode_count + "<br />";
            info += "Start date: " + formatDate(episode_events[0].start) + "<br />";
            info += "End date: " + formatDate(episode_events[episode_events.length-1].start) + "<br />";
            info += "Number of seasons: " + season_count + "<br />";
            info += "Total watch time: " + total_runtime + " mins<br />";
            info += "Average episode length: " + Math.floor(total_runtime/episode_count) + "mins<br />";

            $("#info").html(info);
            


    }

    

    //Distrbute episodes accross week
    function getEpisodeDistribution(totalEpisodes){
        var week = [0,0,0,0,0,0,0];
        var day = 0;

        for (e = 0; e < totalEpisodes; e++) {
            week[day]++; //add an episode to day
            if(day < 6){
                day++; //next day
            } else {
                day = 0; //next week
            }
        }
        return week;
    }

    $( "#key_date" ).datepicker().datepicker("setDate", new Date()).datepicker('option','dateFormat','dd/mm/yy');;

    $("#create_schedule").click(function(e){
        e.preventDefault();
        createEvents();
    });

    //initial population 
    $.getJSON('bcs.json', function(data) { //Better call saul
        episodes = data;
        createEvents();
        $("#show_name").val("Better Call Saul");
    });

    //clear input nitially
    var touched = false;
    $("#show_name").focus(function(){
        if(!touched){
            $(this).val("");
            touched = true;
            $("#show_group").removeClass("searched");
        }
    });


});

Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}

function formatTitle(season, episode){
    return "S" + (season).pad(2) + "E" + (episode).pad(2);
}