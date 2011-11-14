<?php

	/* API Widget Data Class
	
		Written by: Nathan Shubert-Harbison
		Created: November, 14th, 2011
		Written for PHP version: 5.3.6
		
		== USAGE ==
		
		Properties:
			- cache_dir
				Location where the cache is stored
			- cache_file
				Name of cache file. If using multiple instances, set a unique cache file name.
				Default is the domain name of the api call.
			- cache_time
				Defaults to check api hourly.
			- api_url *required
				The actual api request url
			- api_data
				The data object returned by the url request
			- data_type
				If an xml or json data type isn't detected, you can manually set it
				
		Methods:
			- pull_data()
				Make API request and return data object
			- print_data()
				Simply does a print_r with <pre> tags. Usefull for exploring your data object.
					
		Usage example:
			$twitter = new API_widget_data;
			$twitter->api_url = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=twitterapi&count=1";
			$twitter->pull_data();
			$twitter->print_data();
			
	*/


class API_widget_data {

	public $cache_dir = "cache";
	public $cache_file;
	public $cache_time = 3600;
	public $api_url = '';
	public $api_data;
	public $data_type;

	function API_widget_data() {

	}

	
	function pull_data() {
	
		$working_directory = dirname(__FILE__);
		$cache_path = $working_directory . "/" . $this->cache_dir;

		
		// Check if API call is requesting JSON or XML
		if ( strstr($this->api_url, "json") ) {
			$this->data_type = "json";
		} elseif ( strstr($this->api_url, "xml") ) {
			$this->data_type = "xml";
		}
		

		// Check if a cache directory exists and create it if not.
		if ( file_exists($cache_path) && is_dir($cache_path) ) {
			chmod($cache_path, 0755);
		} else {
			mkdir($this->cache_dir, 0755);
		}


		// Check if the cache file exists and  create it if not.
		
		if ( $this->cache_file == '' ) {
			$parse_url = parse_url($this->api_url);
			$this->cache_file = $parse_url['host'];
			$this->cache_file = ltrim($this->cache_file, "api.");
			$this->cache_file = rtrim($this->cache_file, ".com");
		}
		
	  	$cache_file = $cache_path . "/" . $this->cache_file . "." . $this->data_type;
	  	if ( !file_exists($cache_file) ) {
	  		fopen($cache_file, "w+");
			chmod($cache_file, 0755);
	  	}
	
	
		// Determine how old the cache is and if it's recent enough, use it. Otherwise, make a new
		// call, and store the data in the cache
		$cache_time_dif = @(time() - filemtime($cache_file));
		if (file_exists($cache_file) && $cache_time_dif < $this->cache_time && file_get_contents($cache_file) != '') {
			if ( $this->data_type == "json" ) {
				$api_data_contents = file_get_contents($cache_file);
			}
		} else {
			$api_data_contents = $this->curl_get_contents($this->api_url);
			file_put_contents($cache_file, $api_data_contents);
		}
		
		
		// Return data
		if ( $this->data_type == "json" ) {
			$this->api_data = json_decode($api_data_contents);
		} elseif ( $this->data_type == "xml" ) {
			$this->api_data = simplexml_load_file($cache_file);
		} else {
			$this->api_data = "<p>That api url doesn't seem to be working. I know, bummer right? We probably just don't know what type of data is being returned. Try setting the data_type property to xml or json.</p>";
		}
	
	} // data()
	
	
	function print_data() {
		echo "<pre>";
			print_r($this->api_data);
		echo "</pre>";
	}
		
	
	function curl_get_contents($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	} // curl_get_contents()

}

?>