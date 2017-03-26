<?require_once "includes.php";?>
<!DOCTYPE html>
<!-- get out of my source code! -->

<html lang="en-US">
	<head>
		<title>Welcome back, Ryan.</title>

		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />

		<!-- JQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="weather/jquery.simpleWeather.js"></script>
	</head>
	<body>

	<?php
	$authorized = false;
	$permlevel = 0;
		
		if(isset($_GET['key'])){
			$key = $_GET['key'];
			if($key == $_KEY) {
				$authorized = true;
				$permlevel = 999;
			} else if($key == 'demo') {
				$authorized = true;
				$permlevel = 1;
				echo "<div class='demo'></div>";
			}
		}

		if(!$authorized) {
			die("<div class='wrapper'>Not authorized.</div>");
		}


		$_NAME = "Ryan";
		//$_GREETINGS = array("Hello", "Greetings", "Hey", "Bonjour", "Guten Tag", "Hola", "Namaste");
		//$greeting = $_GREETINGS[rand(0, count($_GREETINGS)-1)];

	?>
		<!-- Simply Weather App -->
		<script>
			$(document).ready(function() {
			  $.simpleWeather({
			    location: 'Mississauga, ON',
			    woeid: '',
			    unit: 'c',
			    success: function(weather) {
			      html = '<h2><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</h2>';
			  
			      $("#weather").html(html);
			    },
			    error: function(error) {
			      $("#weather").html('<p>'+error+'</p>');
			    }
			  });
			});
		</script>

		<div class="wrapper">
			<h1 class="title"><span class='darken'>Hey, </span><?=$_NAME?>.</h1>

			<div class="form">
				<form action="#" method="POST">
					<center><input type="text" name="home_search" class="textbox largetext" id='large' placeholder='Where would you like to go today?' autocomplete='off' autofocus /><br />
					<div class='helptext'>Usage: site:&lt;website&gt;, reddit:&lt;subreddit&gt;, google:&lt;term&gt;, twitch:&lt;username&gt;, or a common tag (i.e. email).</div>
					</center>

				</form>
			</div><?php
				if(isset($_POST['home_search'])) {
					$_search = strtolower(mysql_real_escape_string($_POST['home_search']));
					$searchOptions = mysql_query("SELECT * FROM `search_results`");
					$_count = 0;

					if(substr($_search, 0, 7) == "google:") {
						header("Location: http://google.ca/#q=".substr($_search, 7, strlen($_search)+1));
					} else if(substr($_search, 0, 5) == "site:") {
						header("Location: http://" . substr($_search, 5, strlen($_search)+1));
					} else if(substr($_search, 0, 7) == "reddit:") {
						header("Location: http://reddit.com/r/".substr($_search, 7, strlen($_search)+1));
					} else if(substr($_search, 0, 8) == "twitter:") {
						header("Location: http://twitter.com/".substr($_search, 8, strlen($_search)+1));
					} else if(substr($_search, 0, 7) == "twitch:") {
						header("Location: http://twitch.tv/".substr($_search, 7, strlen($_search)+1));
					} else if(substr($_search, 0, 8) == "youtube:") {
						header("Location: http://youtube.com/results?search_query=/".substr($_search, 8, strlen($_search)+1));
					}  else {
						while($searchInfo = mysql_fetch_object($searchOptions)) {
							$_tags = explode(",", $searchInfo->tags);
							if(in_array(strtolower($_search), $_tags)) {
								if($permlevel >= $searchInfo->permlevel) {
									mysql_query("UPDATE `search_results` SET `count` = `count` + 1 WHERE `id`=$searchInfo->id");
									header("Location: " . $searchInfo->url_location);
								}
							} else {
								$_count += 1;
							}
						}
						if($_count == mysql_num_rows($searchOptions)) {
							echo "
							<div class='error'><b>Incorrect Syntax (or tag not found!):</b><br />
							To Google a term, type \"google: term\".<br />
							To go directly to a website, type \"site: website\".<br />
							To go directly to a subreddit, type \"reddit: subreddit\"<br />
							Or type in a common tag to go directly to the website (i.e. \"email\" to go to your email).
							</div>";
						}
					}
				}?>

			<table style='margin-top: 100px; padding: 0px; width: 800px; border-spacing: 30px;'>
				<tr valign='top'>
					<td class='appbox'><div class='appboxtitle'>Today's Weather</div><div id='weather'></div></td>
				</tr>
			</table>
		</div>
	</body>
</html>