# APIzr
	
This PHP class is a handly little tool for making API widgets like Twitter, Flickr, etc. Although there's a virtually limitless number of things you can do with API services, I find that simple widgets are extremely commmon. The groundwork for all of them is usually the same too: make an api call using curl, cache your data, and return a data object. This class does that work, returning an xml or json object that you can use to make your widget with.

## Usage

To use this class, simply create a new instance and pass in an argument list. The only required argument is the api_url. 

### Example

	require_once "apizr.php";

	$twitter = new APIzr(array(
		"api_url" => "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=twitterapi&count=1",
		));

Your $twitter variable now contains the data from your api call. If you want to print out the data to find what you need you can use the print_data() method.
	
	$twitter->print_data();

The following other properties can also be used in your property list.

### Properties

* *api_url* *required - The actual api request url
* *cache_dir* - Location where the cache is stored
* *cache_file* - Name of cache file. If using multiple instances, set a unique cache file name. Default is the domain name of the api call.
* *cache_time* - Defaults to check api hourly.
* *data_type* - If an xml or json data type isn't detected, you can manually set it

Default values:
	"cache_dir" => "cache",
	"cache_time"" => "3600",
				
### Methods
* *print_data()* - Simply does a print_r with pre tags. Usefull for exploring your data object.

## Twitter
I've found that the most frequent use of an api call for a widget is for a Twitter widget. You can pass in an a "twitter" argument as an array of options for twitter to streamline this process. Tweets are printed in a list by default, or you can just return the data and use it in the same way with normal usage.

Example:

	require_once "apizr.php";
	
	$twitter_feed = new APIzr(array(
			"twitter" => array(
				"username" => "twitterapi",
				"number_of_tweets" => 1
			)

Available arguments with default values:

	$twitter = array(
		"number_of_tweets" => 3,
		"display_tweets" => TRUE,
		"clickable_links" => TRUE,
		"twitter_link" => "&rarr;"
		)

*twitter_link* - true, false, "link text".

The only required argument is "username"
					

## Data Types

The class supports xml and json, and will try to identify which one is being requested, but sometimes you'll need to set that manually using the data_type property before running the pull_data method.

## Caching
The data returned from the api call is cached in a /cache directory (you don't need to create it yourself). If your cache folder isn't created automatically you'll need to make your working directory writable.

The default is that the cache lasts for an hour. If you need to you can set the cache_time property to something more frequent, but for the majority of situations once an hour is sufficient. 

## OAuth
Unfortunately you'll need some bigger guns for an API requiring a complicated OAuth situation at the moment.