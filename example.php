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

	require_once("apipenguin.php");

	echo "<h2>Twitter JSON</h2>";
	$twitter = new APIPenguin(array(
		"api_url" => "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=twitterapi&count=1",
		));
	$twitter->print_data();
	
	echo "<h2>Twitter XML</h2>";
	$twitter_xml = new APIPenguin(array(
		"api_url" => "https://api.twitter.com/1/statuses/user_timeline.xml?include_entities=true&include_rts=false&screen_name=twitterapi&count=1",
		));
	$twitter_xml->print_data();
	
	echo "<h2>Twitter User Feed</h2>";
	$twitter_feed = new APIPenguin(array(
		"twitter" => array(
				"username" => "nathanshubert",
//				"number_of_tweets" => 2,
//				"clickable_links" => false,
//				"display_tweets" => false,
//				"twitter_link" => false
			)

		));
	
	echo "<h2>Flickr</h2>";
	$flickr = new APIPenguin(array(
		"api_url" => "http://api.flickr.com/services/rest/?method=flickr.interestingness.getList&api_key=7e821c3288da47d4585889fbd53b0bca&format=rest&api_sig=9ff1b26b7d1eb1e47bce048f6e01c178",
		"data_type" => "xml"
		));

	?>
	
</html>
