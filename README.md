# APIPenguin
This PHP class is a handy little tool consuming data from an API and caching it. This is faster than hitting the API on every page laod and deals with API usage limits. This is specifically designed for content widgets and feeds.

## Usage
To use this class, simply create a new instance and pass in an argument list. The only required argument is the `api_url`. 

### Example

	require_once 'APIPenguin.class.php';

	$book = new APIPenguin(array(
		'api_url' => 'https://www.googleapis.com/books/v1/volumes?q=isbn:0747532699'
	));

Your `$book->data` property now contains the data from your API call. If you want to print out the data for exploration you can use the `print_data()` method.
	
	$book->print_data();

The following other properties can also be used in your property list.

### Arguments

Arguments are entered in an associative array.

* `api_url` *required* - The actual API request url
* `cache_dir` - Defaut: `cache`. Location where the cache is stored
* `cache_file` - Name of cache file. If using multiple instances, set a unique cache file name. Default is the domain name of the API call.
* `cache_time` - Default: `3600` (hourly). Cache expiration time in seconds.
* `data_type` - Default: `json`. If an XML or JSON data type isn't detected, you can manually set it

Default values:

	'cache_dir' => 'cache',
	'cache_time' => '3600',
				
### Methods
* `print_data()` - Simply does a print_r with pre tags. Usefull for exploring your data object.

## Data Types

The class supports XML and JSON, and will try to identify which has been returned, but sometimes you may need to set that manually using the `data_type` argument.

## Caching
The data returned from the API call is cached in a `cache` directory (you don't need to create it yourself). If your cache folder isn't created automatically you'll need to create it manually and make it writable.

The default is that the cache lasts for an hour. If you need to you can set the `cache_time` property to something more frequent.