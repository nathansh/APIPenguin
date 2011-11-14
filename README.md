# API Widget Data Class
	
Although there's a virtually limitless number of things you can do with API services, I find that simple widgets with data from such services is Twitter, Flickr, Instagram, LastFM, etc are extremely commmon. The groundwork for all of these types of widgets is the same too: make an api call using curl, cache your data, and return a data object. This class does that work, returning an xml or json object that you can use to make your widget with.

## USAGE

### Properties:

* *api_url* *required - The actual api request url
* *api_data* - The data object returned by the url request
* *cache_dir* - Location where the cache is stored
* *cache_file* - Name of cache file. If using multiple instances, set a unique cache file name. Default is the domain name of the api call.
* *cache_time* - Defaults to check api hourly.
* *data_type* - If an xml or json data type isn't detected, you can manually set it
				
### Methods:
* *pull_data()* - Make API request and return data object
* *print_data()* - Simply does a print_r with pre tags. Usefull for exploring your data object.
					
### Usage example:

	$twitter = new API_widget_data;
	$twitter->api_url = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name=twitterapi&count=1";
	$twitter->pull_data();
	$twitter_data = $twitter->api_data;

## Data Types

The class supports xml and json, and will try to identify which one is being requested, but sometimes you'll need to set that manually using the data_type property before running the pull_data method.

## Caching
The data returned from the api call is cached in a /cache directory (you don't need to create it yourself). The default is that the cache lasts for an hour. If you need to you can set the cache_time property to something more frequent, but for the majority of situations once an hour is sufficient. 