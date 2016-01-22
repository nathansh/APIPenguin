<?php

/*

	APIPenguin

	Written by: Nathan Shubert-Harbison
	Released under the WTFPL license - http://sam.zoy.org/wtfpl/

	For usage instructions see README.md

*/


class APIPenguin {

	public $cache_dir = "cache";
	public $cache_file;
	public $cache_time = 3600;
	public $api_url = '';
	public $api_data;
	public $data_type = 'json';
	public $manual_recache = false;

	private $cache;
	private $api_data_contents;
	private $args;

	function __construct($args = "") {
		
		if ( $args ) {

			// Deal with the arguments
		
			$this->args = $args;

			$args_to_check = array(
				'api_url',
				'cache_dir',
				'cache_file',
				'cache_time',
				'data_type',
				'manual_recache'
			);

			foreach ( $args_to_check as $arg ) {
				$this->check_arg($arg);
			}

		}
		
		if ( $this->api_url ) {

			// Check if a cache directory exists and create it if not.
			if ( !file_exists($this->cache_dir) && !is_dir($this->cache_dir) ) {
				if ( !mkdir($this->cache_dir, 0755) ) {
					die(
						'<h3>APIPenguin error:</h3>
						<p>The directory <code>' . $this->cache_dir . '</code> could not be found or created.</p>'
						);
				};
			}

			// Check if the cache file exists and  create it if not.
			if ( $this->cache_file == '' ) {
				$parse_url = parse_url($this->api_url);
				$this->cache_file = $parse_url['host'];
				$this->cache_file = ltrim($this->cache_file, "api.");
				$this->cache_file = ltrim($this->cache_file, "www.");
				$this->cache_file = rtrim($this->cache_file, ".com");
			}
			
			$this->cache = $this->cache_dir . "/" . $this->cache_file . ".txt";
			if ( !file_exists($this->cache) ) {
				if ( ! $file_pointer = fopen($this->cache, "w+") ) {
					die(
						'<h3>APIPenguin error:</h3>
						<p>The cache file <code>' . $this->cache . '</code> is not writable :( You probably need to make the <code>' . $this->cache_dir . '</code> directory writable.</p>'
						);
				}
				fclose($file_pointer);
			}
		
			// Use the cache file if it's usable, or otherwise hit the API
			$use_cache = $this->should_use_cache_file($this->cache);

			if ( $use_cache ) {

				// Get cache file contents
				$this->data = file_get_contents($this->cache);

				// Set the data type
				$this->set_data_type_from_cache();

			} else {

				// Hit the api
				$this->hit_api();

			}

			// Return data
			if ( $this->data_type == "json" ) {
				$this->data = json_decode($this->data);
			} elseif ( $this->data_type == "xml" ) {
				$this->data = simplexml_load_string($this->data);
			} else {
				$this->data = "<p>That api url doesn't seem to be working. I know, bummer right?";
			}

			return $this->data;
		
 		}
		
	} // API_widget_data()
	
	
	/**
	 * Print data for debug
	 */
	function print_data() {
		echo "<pre>";
			print_r($this->data);
		echo "</pre>";
	}
	

	/**
	 * Check if the cache file should be used
	 * 
	 */
	private function should_use_cache_file($file) {

		// First, manual mode
		if ( $this->manual_recache ) {
			return file_exists($file) && file_get_contents($file) != '';
		}

		// Is the cache file still valid
		$cache_time_dif = @(time() - filemtime($file));
		return file_exists($file) && $cache_time_dif < $this->cache_time && file_get_contents($file) != '';

	}


	/**
	 * Set the data_type based on cached file contents
	 * 
	 */
	private function set_data_type_from_cache() {

		$this->data_type = substr($this->data, 0, 1) == '{' ? 'json' : 'xml';

	}


	/**
	 * Process arguments
	 * 
	 */
	private function check_arg($arg) {
		if ( isset($this->args[$arg]) ) {
			$this->$arg = $this->args[$arg];
		}
	}


	/**
	 * Get data from the API and cache it
	 * 
	 */
	private function hit_api() {

		// Set the return from the API as the data to use
		$this->data = $this->curl_get_contents($this->api_url);

		// Store the API's data for next time
		file_put_contents($this->cache, $this->data);

	}


	/**
	 * Manually refresh the cache
	 * 
	 */
	function refresh_cache() {

		// Hit the API
		$this->hit_api();

	}

	
	/**
	 * The actual API call with CURL
	 */
	function curl_get_contents($url) {

		// Setup CuRL and get the result
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		// Separate headers
		list($headers, $content) = explode("\r\n\r\n", $output, 2);

		// Get the application type
		preg_match('/(?<=Content-Type: application\/)(.*)(?=; )/', $headers, $type);

		// Set the data type
		$this->data_type = $type[0];

		return $content;

	} // curl_get_contents()

}

?>