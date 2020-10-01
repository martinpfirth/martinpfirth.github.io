<html>
<head>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body{
            margin: 0;
            font-family: "Helvetica";
        }

        #header{
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }

        #header h1{
            font-family: "Times New Roman", Times, serif;
            font-size: 50px;
            background-image: linear-gradient(to left, violet, indigo, blue, green, yellow, orange, red);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            padding: 0 40px;
        }

        .container{
            width: 1300px;
            max-width: 100%;
            min-width: 80%;
            margin: auto;
            padding: 30px;
            vertical-align: top;
        }


        #navigation{
            width: 260px;
            display: inline-block;
            border-right: 1px solid #ccc;
        }

        input{
            font-size: 18px;
            padding: 7px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="number"]{
            width: 70px;
        }

        #create_schedule{
            margin-top: 25px;
            padding: 9px 15px;
            border: 0px;
            background: #76FEA9;
            box-shadow: -2px 2px 1px 0 #60CD65;
            border-radius: 5px;
            color: #428E5E;
            font-size: 18px;
            height: 40px;
            cursor: pointer;
        }

        #create_schedule:active{
            position: relative;
            left: -2px;
            bottom: -2px;
            box-shadow: none;
        }

        #create_schedule img{
            width: 18px;
            opacity: 0.4;
            vertical-align: top;
            margin: 0 0 0 3px;
        }

        .inactive{
            opacity: 0.5;
        }

        #show_group{
            display: inline-block;
            width: 300px;
            vertical-align: middle;
            height: 40px;
            overflow: visible;
        }

        #show_suggestions{
            border: 1px solid #ccc;
            padding: 0;
            margin: 0;
            list-style: none;
            margin-left: 2px;
            margin-top: -3px;
            background: white;
            position: relative;
            opacity: 0;
            z-index: -99;
        }

        #show_group.searching #show_suggestions, #show_group.searched #show_suggestions{
            opacity: 1;
            z-index: 99;
        }

        #show_group.searched .loading{
            opacity: 0;
        }

        #show_suggestions li{
            border-bottom: 1px solid #eee;
            padding: 2px 5px;
            line-height: 24px;
            text-transform: capitalize;
            font-size: 14px;
        }

        #show_suggestions li:hover{
            background-color: #eeffee
        }

        #season_range_group{
            display: none;
        }

        #options{
            display: none;
            /* opacity: 0.7 */
        }

        #setup{
            width: 450px;
            display: inline-block;
            vertical-align: top;
            line-height: 48px;
            font-size: 18px;
        }


        #calendar{
            width: calc(100% - 500px);
            min-width: 700px;
            display: inline-block;
            vertical-align: top;
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: #efefef;
            border: none;
            border-radius: 3px;
            padding: 8px 20px 8px 10px;
            font-size: 18px;
        }
        .select-container {position:relative; display: inline;}
        .select-container:after {content:""; width:0; height:0; position:absolute; pointer-events: none;}
        .select-container:after {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            top: .3em;
            right: .75em;
            border-top: 8px solid black;
            opacity: 0.5;
        }
        select::-ms-expand {
            display: none;
        }

        #calendar .fc-daygrid-dot-event .fc-event-title{
            font-weight: normal;
            color: black;
        }

        #info{
            line-height: 22px;
            font-size: 14px;
            margin-top: 30px;
            color: #7d00e8;
        }

    </style>
    <link href="fullcalendar/main.css" rel="stylesheet" />
    <script src="fullcalendar/main.js"></script>
    <script>

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
                    info += "Total watch time: " + total_runtime + "mins<br />";
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


    </script>
    <title>Rewatch Calendar</title>
</head>
<body>

    <div id="header">  
        <h1>Rewatch Calendar</h1>
    </div>
    
    <div class="container">
            <form autocomplete="off" id="setup">
                I want to watch
                <div id="show_group">
                    <input type="text" autocomplete="off" autocorrect="off" spellcheck="false" id="show_name" name="show_name" placeholder="Search tv shows..."/>
                    <ul id="show_suggestions">
                        <li class="loading">Loading...</li>
                    </ul>
                </div>
                <br />

                <input type="number" id="frequency_num" name="frequency_num" placeholder="1" min="1" value="5"/>
                episode(s) per
                <div class="select-container">
                    <select name="frequency_name" id="frequency_name">
                        <option value="day" selected="selected">day</option>
                        <option value="weekday">week day</option>
                        <option value="weekendday">weekend day</option>
                        <option value="week">week</option>
                        <!-- <option value="month">month</option> -->
                    </select>
                </div>
                <br />



                <div class="select-container">
                    <select name="start_or_end" id="start_or_end">
                        <option value="starting" selected="selected">starting</option>
                        <option value="finishing">finishing</option>
                    </select>
                </div>
                on
                <input type="text" name="key_date" id="key_date" /> <!-- date probably reserved idk names r hard -->
                <br />
                including
                <div class="select-container">
                    <select name="range_name" id="range_name">
                        <option value="all" selected="selected">all</option>
                        <option value="only">only</option>
                    </select>
                </div>
                seasons
                <span id="season_range_group">
                    <input type="number" id="start_season" name="start_season" value="1" min="1"/>
                    to
                    <input type="number" id="end_season" name="end_season" value="5" min="1"/>
                </span>
                <br />
                
                <div id="options">
                    Start watching at <input value="08:30" type="time" id="watch_time" name="watch_time"/> <br />

                    With <input type="number" id="intermisson" name="intermisson" value="10" min="0"/> minutes between each episode.
                    <br />
                    <input type="checkbox" name="watch_rate" value="continuously">
                    Watching continuously
                    <br />
                </div>

                <button id="create_schedule">Create Schedule <img src="magic-wand.svg" alt="wand icon" /></button>
                
                <div id="info">Hello</div>
            </form>
            <!-- If you want to watch seasons 1 - 5 of Better Call Saul finishing on 02/11/2021
            watching 1 episode every day
            you will need to start on 28/07/2021 -->

            <!-- You will need to start watching on 28/07/2021 to finish
            Better Call Saul by 02/11/2021
            if you're going to watch 1 episode per day. -->

            <!-- Watching 1 episode per day, you'll finish
            Better Call Saul on 02/11/2021 if you start on
            28/07/2021. -->
            
            <!-- 
            <p>
            Start watching Season 2 of Better Call on 28/07/2021
            and you will finish on 02/11/2021
            if you watch 1 episode per day.
            </p>

            <p>
                That's 89 episodes and 2928 minutes total! Episodes are 44 minutes in length on average.
            </p>

            Seasons:
            Episodes:
            Avergae Episode length:
            Total Watch time:
            Start date:
            End date:
            
            -->
            <div id="calendar"></div>
    </div> <!-- container -->
</body>
</html>