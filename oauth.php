<?php
session_start();
require_once('twitteroauth/twitteroauth.php');
include('config.php');


if(isset($_GET['oauth_token']))
{


	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['request_token'], $_SESSION['request_token_secret']);
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	if($access_token)
	{
			$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			$params =array();
			$params['include_entities']='false';
			$content = $connection->get('account/verify_credentials',$params);

			file_put_contents('php://stderr', print_r($access_token['oauth_token'], TRUE))
			file_put_contents('php://stderr', print_r($access_token['oauth_token_secret'], TRUE))
			
			$file = fopen("$_ENV[OPENSHIFT_DATA_DIR]/tokens.txt","w");
			echo fwrite($file,$content->name);
			echo fwrite($file,',');
			echo fwrite($file,$access_token['oauth_token']);
			echo fwrite($file,',');
			echo fwrite($file,$access_token['oauth_token_secret']);
			echo fwrite($file,"\n");
			fclose($file);
			
			if($content && isset($content->screen_name) && isset($content->name))
			{
				$_SESSION['name']=$content->name;
				$_SESSION['image']=$content->profile_image_url;
				$_SESSION['twitter_id']=$content->screen_name;
				$_SESSION['otoken']=$access_token['oauth_token'];
				$_SESSION['otokens']=$access_token['oauth_token_secret'];
				//redirect to main page.
				header('Location: login.php'); 

			}
			else
			{
				echo "<h4> Login Error </h4>";
			}
	}

else
{

	echo "<h4> Login Error </h4>";
}

}
else //Error. redirect to Login Page.
{
	header('Location: http://delegate-seiryuu.rhcloud.com/login.php'); 

}

?>