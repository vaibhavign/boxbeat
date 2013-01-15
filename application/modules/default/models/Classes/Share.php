<?php
class Default_Model_Classes_Share extends Default_Model_Classes_UserProfile
{
	protected $shareArray;
	public function __construct($user_id)
	{
		parent::__construct($user_id);
		define('CONSUMER_KEY', 'IctRvYbKFbfrNDTbc3M2ew');//for twitter
		define('CONSUMER_SECRET', 'dhUEcEkh5LZFVUIAj3OF9eUgT7nzvzaYATC03W6DBAo');//for twitter
		define('OAUTH_CALLBACK', 'http://iglobul.com/deals/callback.php');//for twitter
		define('FB_APIKEY', '121876331215915');//for facebook
		define('FB_SECRET', '9a74fcb4b73ecabc7b2c9368efd35777');//for facebook
		$this->shareArray = array();
	}
	public function __get($request)
	{
		return $this->$request;
	}
	public function setSharePluginsData($_post_array)
	{
		extract($_post_array);
		$type_arr = array('profile'=>0,'deal'=>1,'need'=>2,'comments'=>3);
		$short_code = DataRender::getFieldsVal("shortcode","shortcode","user_deal_id = $id and codetype = '$type_arr[$type]'");
		switch($type)
		{
			case "profile":
			case "deal":
			case "need":
			case "comments":
							$facebook_text = "this is default facebook text";
							$twitter_text = "this is default twitter text";
							$email_text = "this is default email text";
							$email_subject = "this is defaukt email subject";
							$inbox_text = "this is default inbox text";
							$url_text = "http:\\goo2o.com\\".$short_code;
							break;
		}
		$this->shareArray = array('facebook_text'=>$facebook_text,'twitter_text'=>$twitter_text,'email_text'=>$email_text,'email_subject'=>$email_subject,'inbox_text'=>$inbox_text,'url_text'=>$url_text,'feed_id'=>$id,'feed_type'=>$type,'facebook_flag'=>0,'twitter_flag'=>0);
		
	}
	public function shareActionPerform($_post_array)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		extract($_post_array);
		switch($action)
		{
			case 'tweet':
						
						require_once APPLICATION_PATH.'/includes/twitter/twitteroauth/twitteroauth.php';
						$select = $db->select()->from('shares','*')->where("user_id = '?' and connection_type = '2'",$this->current_user);
						$data = $db->query($select)->fetchAll();
						$access_token = array('oauth_token' => $data['access_token'],'oauth_token_secret' =>  $data['secret_token'],'connection_id' =>  $data['connection_id'],'screen_name' =>  $data['connection_name']);
						$_SESSION['access_token'] = $access_token ;
						/* Create a TwitterOauth object with consumer/user tokens. */
						$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
						$parameters = array('status' => $tweet_text);
						$status = $connection->post('statuses/update', $parameters);
						break;
			case 'share':
						$access_token = DataRender::getFieldsVal('shares',"where user_id = '$this->current_user' and connection_type = '1'");
						echo $access_token;
						break;
			case 'send_message':
						$inbox_data = array('sendto'=>$to_sent,'sendwhom'=>$this->current_user,'subject'=>'','msg'=>$text,'sendtime'=>time(),'modifiedtime'=>time(),'fid'=>$feed_id,'feed_type'=>$feed_type);
						$db->insert('inbox',$inbox_data);
					break;
			case 'send_email':
						$this->setUserInfo();
						$user_array = $this->userArray;
						$mail = new Zend_Mail('utf-8');
						$mail->setSubject($subject);
						$mail->setFrom($user_array['email_address'],'GOO2O');
						$mail_addTo($email_to);
						$mail->setBodyHtml($body);
						//$mail->send();
					break;
		}
	}
	public function twitterCallbackAction()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		require_once APPLICATION_PATH.'/includes/twitter/twitteroauth/twitteroauth.php';
		if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
		  $_SESSION['oauth_status'] = 'oldtoken';
		  	session_start();
			session_destroy();
		}
		
		/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		
		/* Request access tokens from twitter */
		$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
		
		/* Save the access tokens. Normally these would be saved in a database for future use. */
		$_SESSION['access_token'] = $access_token;
		$data = 
		$sql= "insert into shares values(".$_SESSION['customer_id'].",2,".$access_token['user_id'].",'".$access_token['screen_name']."','".$access_token['oauth_token']."','".$access_token['oauth_token_secret']."')";
		echo $sql;
		echo "<pre>";
		print_r($_SESSION);
		exit;
		//mysql_query();
		/* Remove no longer needed request tokens */
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
		/* If HTTP response is 200 continue otherwise send to connect page to retry */
		if (200 == $connection->http_code) {
		  /* The user has been verified and the access tokens can be saved for future use */
		  $_SESSION['status'] = 'verified';
		  header('Location: test_share.php');
		} 
	}
	public function twitterRedirectAction()
	{
		require_once APPLICATION_PATH.'/includes/twitter/twitteroauth/twitteroauth.php';
		require_once APPLICATION_PATH.'/includes/twitter/config.php';
		
		/* Build TwitterOAuth object with client credentials. */
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
		 
		/* Get temporary credentials. */
		$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
		
		/* Save temporary credentials to session. */
		$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		 
		/* If last connection failed don't display authorization link. */
		switch ($connection->http_code) {
		  case 200:
			/* Build authorize URL and redirect user to Twitter. */
			$url = $connection->getAuthorizeURL($token);
			//$this->_redirect($url);
			header('Location: ' . $url); 
			break;
		  default:
			/* Show notification if something went wrong. */
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
	}
}