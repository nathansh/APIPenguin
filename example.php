<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<title>Class For Making API Calls</title>
	</head>
	<body>
		<h1>Class For Making API Calls</h1>
	</body>
	
	<?php

	require_once('APIPenguin.class.php');

	// JSON Example
	echo '<h2>Example:</h2>';
	echo '<p>URL: <code>https://www.googleapis.com/books/v1/volumes?q=isbn:0747532699</code></p>';
	$books = new APIPenguin(array(
		'api_url' => 'https://www.googleapis.com/books/v1/volumes?q=isbn:0747532699'
	));
	$books->print_data();

	// XML Example
	echo '<h2>Example 2:</h2>';
	echo '<p>URL: <code>http://musicbrainz.org/ws/2/discid/I5l9cCSFccLKFEKS.7wqSZAorPU-?toc=1+12+267257+150+22767+41887+58317+72102+91375+104652+115380+132165+143932+159870+174597</code></p>';
	$musicbrainz = new APIPenguin(array(
		'api_url' => 'http://musicbrainz.org/ws/2/discid/I5l9cCSFccLKFEKS.7wqSZAorPU-?toc=1+12+267257+150+22767+41887+58317+72102+91375+104652+115380+132165+143932+159870+174597'
	));
	echo '<pre>';
		print_r($musicbrainz->data);
	echo '</pre>';

	?>
	
</html>
