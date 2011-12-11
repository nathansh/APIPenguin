<?php

	/* APIPenguin
	
		Written by: Nathan Shubert-Harbison, Little Penguin Studios
		Created: November, 14th, 2011
		Last Update: December 10, 2011
		Written for PHP version: 5.3.6
		
		For usage instructions see README.md

	*/


class APIPenguin {

	public $cache_dir = "cache";
	public $cache_file;
	public $cache_time = 3600;
	public $api_url = '';
	public $api_data;
	public $data_type;
	public $twitter = array(
		"number_of_tweets" => 3,
		"display_tweets" => TRUE,
		"username" => "",
		"clickable_links" => TRUE,
		"twitter_link" => "&rarr;"
		);
	
	private $api_data_contents;
	private $args;

	function APIPenguin($args = "") {
		
		if ( $args ) {
		
			$this->args = $args;
			
			$this->check_arg("api_url");
			$this->check_arg("cache_dir");
			$this->check_arg("cache_file");
			$this->check_arg("cache_time");
			$this->check_arg("data_type");
			
			if ( isset($this->args["twitter"]) && $this->api_url == '' ) {
				$this->twitter = array_merge($this->twitter, $this->args["twitter"]);
				
				if ( $this->twitter["username"] == !"" ) {
					$twitter_api_url = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=" . $this->twitter["username"] . "&count=" . $this->twitter["number_of_tweets"];
					$this->api_url = $twitter_api_url;
					$this->cache_file = "twitter." . $this->twitter["username"];
					
				} else {
					echo "<p>If you'd like a Twitter stream you need to provide your username. See README for more info.</p>";
				}
			}						
		}
		
		if ( $this->api_url !== '' ) {
			
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
				$this->api_data_contents = file_get_contents($cache_file);
			}
		} else {
			$this->api_data_contents = $this->curl_get_contents($this->api_url);
			file_put_contents($cache_file, $this->api_data_contents);
		}
		
		
		// Return data
		if ( $this->data_type == "json" ) {
			$this->api_data = json_decode($this->api_data_contents);
		} elseif ( $this->data_type == "xml" ) {
			$this->api_data = simplexml_load_file($cache_file);
		} else {
			$this->api_data = "<p>That api url doesn't seem to be working. I know, bummer right? We probably just don't know what type of data is being returned. Try setting the data_type property to xml or json.</p>";
		}

		if ( isset($this->args["twitter"]) && $this->twitter["display_tweets"] ) {
			$this->display_tweets();
			}			
	
		return $this->api_data;
		
 		} else {
			if ( $this->args["twitter"] == "" ) {
				echo "<p>You need to provide an API url using the api_url property.</p>";
			}
		}
		
	} // API_widget_data()
	
	
	function print_data() {
		echo "<pre>";
			print_r($this->api_data);
		echo "</pre>";
	}
	
	
	private function check_arg($arg) {
		if ( isset($this->args[$arg]) ) {
			$this->$arg = $this->args[$arg];
		}
	}
	
	
	private function display_tweets() {
		echo '<ul class="tweet_list">';

		foreach ( $this->api_data as $twitter ) {
			$tweet = $twitter->text;
			$date = $twitter->created_at;
			$date = date("F jS", strtotime($date));
			$id = $twitter->id;
			$tweet_url = "https://twitter.com/#!/" . $this->twitter["username"] . "/status/" . $id;
		
			echo '<li class="tweet"><ul>';
				echo '<li class="tweet_text">';
					
					if ( $this->twitter["clickable_links"] ) {
						$tweet = $this->twitterify($tweet);
					}
					
					if ( $this->twitter["twitter_link"] ) {
						if ( $this->twitter["twitter_link"] == 1 ) $this->twitter["twitter_link"] = "&rarr;"; 
						$tweet .= ' <a href="' . $tweet_url . '" class="twitter_link">' . $this->twitter["twitter_link"] . '</a>';
					}
					
					echo $tweet;
				echo '</li>';
				echo '<li class="tweet_date">' . $date . '</li>';
			echo '</ul></li>';
		}

		echo '</ul>';
	}
	
	
	function twitterify($ret) {
	  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
	  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
	  $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
	  $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
	return $ret;
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