<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Class For Making API Calls</title>
	</head>
	<body>
		<h1>Class For Making API Calls</h1>
	</body>
	
	<?php

	require_once("class-api_widget_data.php");

	echo "<h2>Twitter JSON</h2>";
	$twitter = new API_widget_data(array(
		"api_url" => "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=twitterapi&count=1",
		));
	$twitter->print_data();
	
	echo "<h2>Twitter XML</h2>";
	$twitter_xml = new API_widget_data(array(
		"api_url" => "https://api.twitter.com/1/statuses/user_timeline.xml?include_entities=true&include_rts=false&screen_name=twitterapi&count=1",
		));
	
	echo "<h2>Flickr</h2>";
	$flickr = new API_widget_data(array(
		"api_url" => "http://api.flickr.com/services/rest/?method=flickr.interestingness.getList&api_key=7e821c3288da47d4585889fbd53b0bca&format=rest&api_sig=9ff1b26b7d1eb1e47bce048f6e01c178",
		"data_type" => "xml"
		));

	?>
	
</html>
