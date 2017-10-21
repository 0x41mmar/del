	<?php

/**
 * twitter-timeline-php : Twitter API 1.1 user timeline implemented with PHP, a little JavaScript, and web intents
 * 
 * @package		twitter-timeline-php
 * @author		Kim Maida <contact@kim-maida.com>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link		http://github.com/kmaida/twitter-timeline-php
 * @credits		Thank you to <http://viralpatel.net/blogs/twitter-like-n-min-sec-ago-timestamp-in-php-mysql/> for base for "time ago" calculations 
 *
**/
	
############################################################### 
	## SETTINGS
	
	// Set access tokens <https://dev.twitter.com/apps/>
	$settings = array(
		'consumer_key' => getenv('consumer_key'),
		'consumer_secret' => getenv('consumer_secret'),
		'oauth_access_token' => getenv('oauth_access_token'),
		'oauth_access_token_secret' => getenv('oauth_access_token_secret')
	);
	
	// Set API request URL and timeline variables if needed <https://dev.twitter.com/docs/api/1.1>
	$url = 'https://api.twitter.com/1.1/followers/list.json';
	$twitterUsername = htmlspecialchars($_GET['username']);
	$tweetCount = 200;
	
	// Use private tokens for development if they exist; delete when no longer necessary
	$tokens = $_SERVER["DOCUMENT_ROOT"] . 'DisplayConsole/_utils/tokens.php';
	is_file($tokens) AND include $tokens;
	
	// Require the OAuth class
	require_once($_SERVER["DOCUMENT_ROOT"] . 'DisplayConsole/_utils/twitter-api-oauth.php');
	
###############################################################
	## MAKE GET REQUEST
	
	$getfield = '?screen_name=' . $twitterUsername . '&count=' . $tweetCount;
	$twitter = new TwitterAPITimeline($settings);
	
	$json = $twitter->setGetfield($getfield)	// Note: Set the GET field BEFORE calling buildOauth()
				  	->buildOauth($url, $requestMethod)
				 	->performRequest();
				 			
	$twitter_data = json_decode($json, true);	// Create an array with the fetched JSON data
	
############################################################### 	
	## DO SOMETHING WITH THE DATA
	
	
//-------------------------------------------------------------- Timeline HTML output
	# This output markup adheres to the Twitter developer display requirements (https://dev.twitter.com/terms/display-requirements)
	
	# Open the timeline list
	echo '<ul id="tweet-list" class="tweet-list">';
	
	# The tweets loop
	foreach ($twitter_data['users'] as $user) {
	
		# Tweet source user (could be a retweeted user and not the owner of the timeline)
		$userName = $user['name'];
		$userScreenName = $user['screen_name'];
		$userAvatarURL = stripcslashes($user['profile_image_url']);
		$userAccountURL = 'http://twitter.com/' . $userScreenName;

?>
				
		<li id="<?php echo 'tweetid-' . $id; ?>" class="tweet">
			<div class="tweet-info">
				<div class="user-info">
					<a class="user-avatar-link" href="<?php echo $userAccountURL; ?>">
						<img class="user-avatar" src="<?php echo $userAvatarURL; ?>">
					</a>
					<p class="user-account">
						<a class="user-name" href="<?php echo $userAccountURL; ?>"><strong><?php echo $userName; ?></strong></a>
						<a class="user-screenName" href="<?php echo $userAccountURL; ?>">@<?php echo $userScreenName; ?></a>
					</p>
				</div>
			</div>
			<blockquote class="tweet-text">
			</blockquote>
		</li>	
			
<?php 
	}	# End tweets loop
	
	# Close the timeline list
	echo '</ul>';
	
	# echo $json; // Uncomment this line to view the entire JSON array. Helpful: http://www.freeformatter.com/json-formatter.html
?>