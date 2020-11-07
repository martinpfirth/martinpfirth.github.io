<html>
<head>
    <script src="jquery.min.js"></script>
    <script src="jquery-ui.js"></script>
    <link rel="stylesheet" href="jquery-ui.min.css">
    <link rel="stylesheet" href="jquery-ui.theme.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="fullcalendar/main.css" rel="stylesheet" />
    <script src="fullcalendar/main.js"></script>
    <script src="scripts.js"></script>

    <title>Watch Calendar</title>
</head>
<body>
    <div id="header">  
        <h1>Watch Calendar</h1>
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

                <button id="create_schedule">Create schedule <img src="magic-wand.svg" alt="wand icon" /></button>

                <div id="info">Hello</div>
            </form>
            <div id="calendar"></div>
    </div> <!-- container -->
</body>
</html>