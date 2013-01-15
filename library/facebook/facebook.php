<?php

// Copyright 2004-2008 Facebook. All Rights Reserved.
//
// +---------------------------------------------------------------------------+
// | Facebook Platform PHP5 client                                 |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2007 Facebook, Inc.                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+
// | For help with this library, contact developers-help@facebook.com          |
// +---------------------------------------------------------------------------+
//
include_once 'facebookapi_php5_restlib.php';

define('FACEBOOK_API_VALIDATION_ERROR', 1);
class Facebook {
    /**
   * The Application ID.
   */
  protected $appId;

  /**
   * The Application API Secret.
   */
  protected $apiSecret;

  /**
   * The active user session, if one is available.
   */
  protected $session;

  /**
   * The data from the signed_request token.
   */
  protected $signedRequest;

  /**
   * Indicates that we already loaded the session as best as we could.
   */
  protected $sessionLoaded = false;

  /**
   * Indicates if Cookie support should be enabled.
   */
  protected $cookieSupport = false;

  /**
   * Base domain for the Cookie.
   */
  protected $baseDomain = '';

  /**
   * Indicates if the CURL based @ syntax for file uploads is enabled.
   */
  protected $fileUploadSupport = false;

  public $api_client;

  public $api_key;
  public $secret;
  public $generate_session_secret;
  public $session_expires;

  public $fb_params;
  public $user;
  public $profile_user;
  public function __construct($api_key, $secret, $generate_session_secret=false) {
    $this->api_key                 = $api_key;
    $this->secret                  = $secret;
    $this->generate_session_secret = $generate_session_secret;
    $this->api_client = new FacebookRestClient($api_key, $secret);

    $this->validate_fb_params();
    if (isset($this->fb_params['friends'])) {
      $this->api_client->friends_list = explode(',', $this->fb_params['friends']);
    }
    if (isset($this->fb_params['added'])) {
      $this->api_client->added = $this->fb_params['added'];
    }
  }

  public function validate_fb_params($resolve_auth_token=true) {
    $this->fb_params = $this->get_valid_fb_params($_POST, 48*3600, 'fb_sig');
    if (!$this->fb_params) {
      $this->fb_params = $this->get_valid_fb_params($_GET, 48*3600, 'fb_sig');
    }
    if ($this->fb_params) {
      // If we got any fb_params passed in at all, then either:
      //  - they included an fb_user / fb_session_key, which we should assume to be correct
      //  - they didn't include an fb_user / fb_session_key, which means the user doesn't have a
      //    valid session and if we want to get one we'll need to use require_login().  (Calling
      //    set_user with null values for user/session_key will work properly.)
      // Note that we should *not* use our cookies in this scenario, since they may be referring to
      // the wrong user.
      $user        = isset($this->fb_params['user'])        ? $this->fb_params['user'] : null;
      $this->profile_user        = isset($this->fb_params['profile_user'])        ? $this->fb_params['profile_user'] : null;
      if (isset($this->fb_params['session_key'])) {
        $session_key =  $this->fb_params['session_key'];
      } else if (isset($this->fb_params['profile_session_key'])) {
        $session_key =  $this->fb_params['profile_session_key'];
      } else {
        $session_key = null;
      }
      $expires     = isset($this->fb_params['expires'])     ? $this->fb_params['expires'] : null;
      $this->set_user($user, $session_key, $expires);
    } else if (!empty($_COOKIE) && $cookies = $this->get_valid_fb_params($_COOKIE, null, $this->api_key)) {
      // use $api_key . '_' as a prefix for the cookies in case there are
      // multiple facebook clients on the same domain.
      $expires = isset($cookies['expires']) ? $cookies['expires'] : null;
      $this->set_user($cookies['user'], $cookies['session_key'], $expires);
    } else if (isset($_GET['auth_token']) && $resolve_auth_token &&
               $session = $this->do_get_session($_GET['auth_token'])) {
      $session_secret = ($this->generate_session_secret && !empty($session['secret'])) ? $session['secret'] : null;
      $this->set_user($session['uid'], $session['session_key'], $session['expires'], $session_secret);
    }

    return !empty($this->fb_params);
  }

  // Store a temporary session secret for the current session
  // for use with the JS client library
  public function promote_session() {
    try {
      $session_secret = $this->api_client->auth_promoteSession();
      if (!$this->in_fb_canvas()) {
        $this->set_cookies($this->user, $this->api_client->session_key, $this->session_expires, $session_secret);
      }
      return $session_secret;
    } catch (FacebookRestClientException $e) {
      // API_EC_PARAM means we don't have a logged in user, otherwise who
      // knows what it means, so just throw it.
      if ($e->getCode() != FacebookAPIErrorCodes::API_EC_PARAM) {
        throw $e;
      }
    }
  }

  public function do_get_session($auth_token) {
    try {
      return $this->api_client->auth_getSession($auth_token, $this->generate_session_secret);
    } catch (FacebookRestClientException $e) {
      // API_EC_PARAM means we don't have a logged in user, otherwise who
      // knows what it means, so just throw it.
      if ($e->getCode() != FacebookAPIErrorCodes::API_EC_PARAM) {
        throw $e;
      }
    }
  }

  // Invalidate the session currently being used, and clear any state associated with it
  public function expire_session() {
    if ($this->api_client->auth_expireSession()) {
      if (!$this->in_fb_canvas() && isset($_COOKIE[$this->api_key . '_user'])) {
        $cookies = array('user', 'session_key', 'expires', 'ss');
        foreach ($cookies as $name) {
          setcookie($this->api_key . '_' . $name, false, time() - 3600);
          unset($_COOKIE[$this->api_key . '_' . $name]);
        }
        setcookie($this->api_key, false, time() - 3600);
        unset($_COOKIE[$this->api_key]);
      }

      // now, clear the rest of the stored state
      $this->user = 0;
      $this->api_client->session_key = 0;
      return true;
    } else {
      return false;
    }
  }

  public function redirect($url) {
 
    if ($this->in_fb_canvas()) {
      echo '<fb:redirect url="' . $url . '"/>';
    } else if (preg_match('/^https?:\/\/([^\/]*\.)?facebook\.com(:\d+)?/i', $url)) {
      // make sure facebook.com url's load in the full frame so that we don't
      // get a frame within a frame.
      echo "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
    } else {
      header('Location', $url);
    }
    exit;
  }

  public function in_frame() {
    return isset($this->fb_params['in_canvas']) || isset($this->fb_params['in_iframe']);
  }
  public function in_fb_canvas() {
    return isset($this->fb_params['in_canvas']);
  }

  public function get_loggedin_user() {
    return $this->user;
  }

  public function get_profile_user() {
    return $this->profile_user;
  }

  public static function current_url() {
    return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  // require_add and require_install have been removed.
  // see http://developer.facebook.com/news.php?blog=1&story=116 for more details
  public function require_login() {
    if ($user = $this->get_loggedin_user()) {
      return $user;
    }
    $this->redirect($this->get_login_url(self::current_url(), $this->in_frame()));
  }

  public function require_frame() {
    if (!$this->in_frame()) {
      $this->redirect($this->get_login_url(self::current_url(), true));
    }
  }

  public static function get_facebook_url($subdomain='www') {
    return 'http://' . $subdomain . '.new.facebook.com';
  }

  public function get_install_url($next=null) {
    // this was renamed, keeping for compatibility's sake
    return $this->get_add_url($next);
  }

  public function get_add_url($next=null) {
    return self::get_facebook_url().'/add.php?api_key='.$this->api_key .
      ($next ? '&next=' . urlencode($next) : '');
  }

  public function get_login_url($next, $canvas) {
    return self::get_facebook_url().'/login.php?v=1.0&api_key=' . $this->api_key .
      ($next ? '&next=' . urlencode($next)  : '') .
      ($canvas ? '&canvas' : '');
  }

  public static function generate_sig($params_array, $secret) {
    $str = '';

    ksort($params_array);
    // Note: make sure that the signature parameter is not already included in
    //       $params_array.
    foreach ($params_array as $k=>$v) {
      $str .= "$k=$v";
    }
    $str .= $secret;

    return md5($str);
  }

  public function set_user($user, $session_key, $expires=null, $session_secret=null) {
    if (!$this->in_fb_canvas() && (!isset($_COOKIE[$this->api_key . '_user'])
                                   || $_COOKIE[$this->api_key . '_user'] != $user)) {
      $this->set_cookies($user, $session_key, $expires, $session_secret);
    }
    $this->user = $user;
    $this->api_client->session_key = $session_key;
    $this->session_expires = $expires;
  }

  public function set_cookies($user, $session_key, $expires=null, $session_secret=null) {
    $cookies = array();
    $cookies['user'] = $user;
    $cookies['session_key'] = $session_key;
    if ($expires != null) {
      $cookies['expires'] = $expires;
    }
    if ($session_secret != null) {
      $cookies['ss'] = $session_secret;
    }
    foreach ($cookies as $name => $val) {
      setcookie($this->api_key . '_' . $name, $val, (int)$expires);
      $_COOKIE[$this->api_key . '_' . $name] = $val;
    }
    $sig = self::generate_sig($cookies, $this->secret);
    setcookie($this->api_key, $sig, (int)$expires);
    $_COOKIE[$this->api_key] = $sig;
  }

  /**
   * Tries to undo the badness of magic quotes as best we can
   * @param     string   $val   Should come directly from $_GET, $_POST, etc.
   * @return    string   val without added slashes
   */
  public static function no_magic_quotes($val) {
    if (get_magic_quotes_gpc()) {
      return stripslashes($val);
    } else {
      return $val;
    }
  }

  public function get_valid_fb_params($params, $timeout=null, $namespace='fb_sig') {
    $prefix = $namespace . '_';
    $prefix_len = strlen($prefix);
    $fb_params = array();
    foreach ($params as $name => $val) {
      if (strpos($name, $prefix) === 0) {
        $fb_params[substr($name, $prefix_len)] = self::no_magic_quotes($val);
      }
    }
    if ($timeout && (!isset($fb_params['time']) || time() - $fb_params['time'] > $timeout)) {
      return array();
    }
    if (!isset($params[$namespace]) || (!$this->verify_signature($fb_params, $params[$namespace]))) {
      return array();
    }
    return $fb_params;
  }

  public function verify_signature($fb_params, $expected_sig) {
    return self::generate_sig($fb_params, $this->secret) == $expected_sig;
  }

  public function encode_validationError($summary, $message) {
    return json_encode(
               array('errorCode'    => FACEBOOK_API_VALIDATION_ERROR,
                     'errorTitle'   => $summary,
                     'errorMessage' => $message));
  }

  public function encode_multiFeedStory($feed, $next) {
    return json_encode(
               array('method'   => 'multiFeedStory',
                     'content'  =>
                     array('next' => $next,
                           'feed' => $feed)));
  }

  public function encode_feedStory($feed, $next) {
    return json_encode(
               array('method'   => 'feedStory',
                     'content'  =>
                     array('next' => $next,
                           'feed' => $feed)));
  }

  public function create_templatizedFeedStory($title_template, $title_data=array(),
                                    $body_template='', $body_data = array(), $body_general=null,
                                    $image_1=null, $image_1_link=null,
                                    $image_2=null, $image_2_link=null,
                                    $image_3=null, $image_3_link=null,
                                    $image_4=null, $image_4_link=null) {
    return array('title_template'=> $title_template,
                 'title_data'   => $title_data,
                 'body_template'=> $body_template,
                 'body_data'    => $body_data,
                 'body_general' => $body_general,
                 'image_1'      => $image_1,
                 'image_1_link' => $image_1_link,
                 'image_2'      => $image_2,
                 'image_2_link' => $image_2_link,
                 'image_3'      => $image_3,
                 'image_3_link' => $image_3_link,
                 'image_4'      => $image_4,
                 'image_4_link' => $image_4_link);
  }
    /**
   * Get the data from a signed_request token
   *
   * @return String the base domain
   */
  public function getSignedRequest() {
    if (!$this->signedRequest) {
      if (isset($_REQUEST['signed_request'])) {
        $this->signedRequest = $this->parseSignedRequest(
          $_REQUEST['signed_request']);
      }
    }
    return $this->signedRequest;
  }
/**
   * Set the Application ID.
   *
   * @param String $appId the Application ID
   */
  public function setAppId($appId) {
    $this->appId = $appId;
    return $this;
  }

  /**
   * Get the Application ID.
   *
   * @return String the Application ID
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set the API Secret.
   *
   * @param String $appId the API Secret
   */
  public function setApiSecret($apiSecret) {
    $this->apiSecret = $apiSecret;
    return $this;
  }

  /**
   * Get the API Secret.
   *
   * @return String the API Secret
   */
  public function getApiSecret() {
    return $this->apiSecret;
  }

  /**
   * Set the Cookie Support status.
   *
   * @param Boolean $cookieSupport the Cookie Support status
   */
  public function setCookieSupport($cookieSupport) {
    $this->cookieSupport = $cookieSupport;
    return $this;
  }

  /**
   * Get the Cookie Support status.
   *
   * @return Boolean the Cookie Support status
   */
  public function useCookieSupport() {
    return $this->cookieSupport;
  }

  /**
   * Set the base domain for the Cookie.
   *
   * @param String $domain the base domain
   */
  public function setBaseDomain($domain) {
    $this->baseDomain = $domain;
    return $this;
  }

  /**
   * Get the base domain for the Cookie.
   *
   * @return String the base domain
   */
  public function getBaseDomain() {
    return $this->baseDomain;
  }

  /**
   * Set the file upload support status.
   *
   * @param String $domain the base domain
   */
  public function setFileUploadSupport($fileUploadSupport) {
    $this->fileUploadSupport = $fileUploadSupport;
    return $this;
  }

  /**
   * Get the file upload support status.
   *
   * @return String the base domain
   */
  public function useFileUploadSupport() {
    return $this->fileUploadSupport;
  }

 

  /**
   * Set the Session.
   *
   * @param Array $session the session
   * @param Boolean $write_cookie indicate if a cookie should be written. this
   * value is ignored if cookie support has been disabled.
   */
  public function setSession($session=null, $write_cookie=true) {
    $session = $this->validateSessionObject($session);
    $this->sessionLoaded = true;
    $this->session = $session;
    if ($write_cookie) {
      $this->setCookieFromSession($session);
    }
    return $this;
  }

  /**
   * Get the session object. This will automatically look for a signed session
   * sent via the signed_request, Cookie or Query Parameters if needed.
   *
   * @return Array the session
   */
  public function getSession() {
    if (!$this->sessionLoaded) {
      $session = null;
      $write_cookie = true;

      // try loading session from signed_request in $_REQUEST
      $signedRequest = $this->getSignedRequest();
      if ($signedRequest) {
        // sig is good, use the signedRequest
        $session = $this->createSessionFromSignedRequest($signedRequest);
      }

      // try loading session from $_REQUEST
      if (!$session && isset($_REQUEST['session'])) {
        $session = json_decode(
          get_magic_quotes_gpc()
            ? stripslashes($_REQUEST['session'])
            : $_REQUEST['session'],
          true
        );
        $session = $this->validateSessionObject($session);
      }

      // try loading session from cookie if necessary
      if (!$session && $this->useCookieSupport()) {
        $cookieName = $this->getSessionCookieName();
        if (isset($_COOKIE[$cookieName])) {
          $session = array();
          parse_str(trim(
            get_magic_quotes_gpc()
              ? stripslashes($_COOKIE[$cookieName])
              : $_COOKIE[$cookieName],
            '"'
          ), $session);
          $session = $this->validateSessionObject($session);
          // write only if we need to delete a invalid session cookie
          $write_cookie = empty($session);
        }
      }

      $this->setSession($session, $write_cookie);
    }

    return $this->session;
  }

  /**
   * Get the UID from the session.
   *
   * @return String the UID if available
   */
  public function getUser() {
    $session = $this->getSession();
    return $session ? $session['uid'] : null;
  }

  /**
   * Gets a OAuth access token.
   *
   * @return String the access token
   */
  public function getAccessToken() {
    $session = $this->getSession();
    // either user session signed, or app signed
    if ($session) {
      return $session['access_token'];
    } else {
      return $this->getAppId() .'|'. $this->getApiSecret();
    }
  }

  /**
   * Get a Login URL for use with redirects. By default, full page redirect is
   * assumed. If you are using the generated URL with a window.open() call in
   * JavaScript, you can pass in display=popup as part of the $params.
   *
   * The parameters:
   * - next: the url to go to after a successful login
   * - cancel_url: the url to go to after the user cancels
   * - req_perms: comma separated list of requested extended perms
   * - display: can be "page" (default, full page) or "popup"
   *
   * @param Array $params provide custom parameters
   * @return String the URL for the login flow
   */
  public function getLoginUrl($params=array()) {
    $currentUrl = $this->getCurrentUrl();
    return $this->getUrl(
      'www',
      'login.php',
      array_merge(array(
        'api_key'         => $this->getAppId(),
        'cancel_url'      => $currentUrl,
        'display'         => 'page',
        'fbconnect'       => 1,
        'next'            => $currentUrl,
        'return_session'  => 1,
        'session_version' => 3,
        'v'               => '1.0',
      ), $params)
    );
  }

  /**
   * Get a Logout URL suitable for use with redirects.
   *
   * The parameters:
   * - next: the url to go to after a successful logout
   *
   * @param Array $params provide custom parameters
   * @return String the URL for the logout flow
   */
  public function getLogoutUrl($params=array()) {
    return $this->getUrl(
      'www',
      'logout.php',
      array_merge(array(
        'next'         => $this->getCurrentUrl(),
        'access_token' => $this->getAccessToken(),
      ), $params)
    );
  }

  /**
   * Get a login status URL to fetch the status from facebook.
   *
   * The parameters:
   * - ok_session: the URL to go to if a session is found
   * - no_session: the URL to go to if the user is not connected
   * - no_user: the URL to go to if the user is not signed into facebook
   *
   * @param Array $params provide custom parameters
   * @return String the URL for the logout flow
   */
  public function getLoginStatusUrl($params=array()) {
    return $this->getUrl(
      'www',
      'extern/login_status.php',
      array_merge(array(
        'api_key'         => $this->getAppId(),
        'no_session'      => $this->getCurrentUrl(),
        'no_user'         => $this->getCurrentUrl(),
        'ok_session'      => $this->getCurrentUrl(),
        'session_version' => 3,
      ), $params)
    );
  }

  /**
   * Make an API call.
   *
   * @param Array $params the API call parameters
   * @return the decoded response
   */
  public function api(/* polymorphic */) {
    $args = func_get_args();
    if (is_array($args[0])) {
      return $this->_restserver($args[0]);
    } else {
      return call_user_func_array(array($this, '_graph'), $args);
    }
  }

  /**
   * Invoke the old restserver.php endpoint.
   *
   * @param Array $params method call object
   * @return the decoded response object
   * @throws FacebookApiException
   */
  protected function _restserver($params) {
    // generic application level parameters
    $params['api_key'] = $this->getAppId();
    $params['format'] = 'json-strings';

    $result = json_decode($this->_oauthRequest(
      $this->getApiUrl($params['method']),
      $params
    ), true);

    // results are returned, errors are thrown
    if (is_array($result) && isset($result['error_code'])) {
      throw new FacebookApiException($result);
    }
    return $result;
  }

  /**
   * Invoke the Graph API.
   *
   * @param String $path the path (required)
   * @param String $method the http method (default 'GET')
   * @param Array $params the query/post data
   * @return the decoded response object
   * @throws FacebookApiException
   */
  protected function _graph($path, $method='GET', $params=array()) {
    if (is_array($method) && empty($params)) {
      $params = $method;
      $method = 'GET';
    }
    $params['method'] = $method; // method override as we always do a POST

    $result = json_decode($this->_oauthRequest(
      $this->getUrl('graph', $path),
      $params
    ), true);

    // results are returned, errors are thrown
    if (is_array($result) && isset($result['error'])) {
      $e = new FacebookApiException($result);
      switch ($e->getType()) {
        // OAuth 2.0 Draft 00 style
        case 'OAuthException':
        // OAuth 2.0 Draft 10 style
        case 'invalid_token':
          $this->setSession(null);
      }
      throw $e;
    }
    return $result;
  }

  /**
   * Make a OAuth Request
   *
   * @param String $path the path (required)
   * @param Array $params the query/post data
   * @return the decoded response object
   * @throws FacebookApiException
   */
  protected function _oauthRequest($url, $params) {
    if (!isset($params['access_token'])) {
      $params['access_token'] = $this->getAccessToken();
    }

    // json_encode all params values that are not strings
    foreach ($params as $key => $value) {
      if (!is_string($value)) {
        $params[$key] = json_encode($value);
      }
    }
    return $this->makeRequest($url, $params);
  }

  /**
   * Makes an HTTP request. This method can be overriden by subclasses if
   * developers want to do fancier things or use something other than curl to
   * make the request.
   *
   * @param String $url the URL to make the request to
   * @param Array $params the parameters to use for the POST body
   * @param CurlHandler $ch optional initialized curl handle
   * @return String the response text
   */
  protected function makeRequest($url, $params, $ch=null) {
    if (!$ch) {
      $ch = curl_init();
    }

    $opts = self::$CURL_OPTS;
    if ($this->useFileUploadSupport()) {
      $opts[CURLOPT_POSTFIELDS] = $params;
    } else {
      $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    }
    $opts[CURLOPT_URL] = $url;

    // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
    // for 2 seconds if the server does not support this header.
    if (isset($opts[CURLOPT_HTTPHEADER])) {
      $existing_headers = $opts[CURLOPT_HTTPHEADER];
      $existing_headers[] = 'Expect:';
      $opts[CURLOPT_HTTPHEADER] = $existing_headers;
    } else {
      $opts[CURLOPT_HTTPHEADER] = array('Expect:');
    }

    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);

    if (curl_errno($ch) == 60) { // CURLE_SSL_CACERT
      self::errorLog('Invalid or no certificate authority found, using bundled information');
      curl_setopt($ch, CURLOPT_CAINFO,
                  dirname(__FILE__) . '/fb_ca_chain_bundle.crt');
      $result = curl_exec($ch);
    }

    if ($result === false) {
      $e = new FacebookApiException(array(
        'error_code' => curl_errno($ch),
        'error'      => array(
          'message' => curl_error($ch),
          'type'    => 'CurlException',
        ),
      ));
      curl_close($ch);
      throw $e;
    }
    curl_close($ch);
    return $result;
  }

  /**
   * The name of the Cookie that contains the session.
   *
   * @return String the cookie name
   */
  protected function getSessionCookieName() {
    return 'fbs_' . $this->getAppId();
  }

  /**
   * Set a JS Cookie based on the _passed in_ session. It does not use the
   * currently stored session -- you need to explicitly pass it in.
   *
   * @param Array $session the session to use for setting the cookie
   */
  protected function setCookieFromSession($session=null) {
    if (!$this->useCookieSupport()) {
      return;
    }

    $cookieName = $this->getSessionCookieName();
    $value = 'deleted';
    $expires = time() - 3600;
    $domain = $this->getBaseDomain();
    if ($session) {
      $value = '"' . http_build_query($session, null, '&') . '"';
      if (isset($session['base_domain'])) {
        $domain = $session['base_domain'];
      }
      $expires = $session['expires'];
    }

    // prepend dot if a domain is found
    if ($domain) {
      $domain = '.' . $domain;
    }

    // if an existing cookie is not set, we dont need to delete it
    if ($value == 'deleted' && empty($_COOKIE[$cookieName])) {
      return;
    }

    if (headers_sent()) {
      self::errorLog('Could not set cookie. Headers already sent.');

    // ignore for code coverage as we will never be able to setcookie in a CLI
    // environment
    // @codeCoverageIgnoreStart
    } else {
      setcookie($cookieName, $value, $expires, '/', $domain);
    }
    // @codeCoverageIgnoreEnd
  }

  /**
   * Validates a session_version=3 style session object.
   *
   * @param Array $session the session object
   * @return Array the session object if it validates, null otherwise
   */
  protected function validateSessionObject($session) {
    // make sure some essential fields exist
    if (is_array($session) &&
        isset($session['uid']) &&
        isset($session['access_token']) &&
        isset($session['sig'])) {
      // validate the signature
      $session_without_sig = $session;
      unset($session_without_sig['sig']);
      $expected_sig = self::generateSignature(
        $session_without_sig,
        $this->getApiSecret()
      );
      if ($session['sig'] != $expected_sig) {
        self::errorLog('Got invalid session signature in cookie.');
        $session = null;
      }
      // check expiry time
    } else {
      $session = null;
    }
    return $session;
  }

  /**
   * Returns something that looks like our JS session object from the
   * signed token's data
   *
   * TODO: Nuke this once the login flow uses OAuth2
   *
   * @param Array the output of getSignedRequest
   * @return Array Something that will work as a session
   */
  protected function createSessionFromSignedRequest($data) {
    if (!isset($data['oauth_token'])) {
      return null;
    }

    $session = array(
      'uid'          => $data['user_id'],
      'access_token' => $data['oauth_token'],
      'expires'      => $data['expires'],
    );

    // put a real sig, so that validateSignature works
    $session['sig'] = self::generateSignature(
      $session,
      $this->getApiSecret()
    );

    return $session;
  }

  /**
   * Parses a signed_request and validates the signature.
   * Then saves it in $this->signed_data
   *
   * @param String A signed token
   * @param Boolean Should we remove the parts of the payload that
   *                are used by the algorithm?
   * @return Array the payload inside it or null if the sig is wrong
   */
  protected function parseSignedRequest($signed_request) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig = self::base64UrlDecode($encoded_sig);
    $data = json_decode(self::base64UrlDecode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
      self::errorLog('Unknown algorithm. Expected HMAC-SHA256');
      return null;
    }

    // check sig
    $expected_sig = hash_hmac('sha256', $payload,
                              $this->getApiSecret(), $raw = true);
    if ($sig !== $expected_sig) {
      self::errorLog('Bad Signed JSON signature!');
      return null;
    }

    return $data;
  }

  /**
   * Build the URL for api given parameters.
   *
   * @param $method String the method name.
   * @return String the URL for the given parameters
   */
  protected function getApiUrl($method) {
    static $READ_ONLY_CALLS =
      array('admin.getallocation' => 1,
            'admin.getappproperties' => 1,
            'admin.getbannedusers' => 1,
            'admin.getlivestreamvialink' => 1,
            'admin.getmetrics' => 1,
            'admin.getrestrictioninfo' => 1,
            'application.getpublicinfo' => 1,
            'auth.getapppublickey' => 1,
            'auth.getsession' => 1,
            'auth.getsignedpublicsessiondata' => 1,
            'comments.get' => 1,
            'connect.getunconnectedfriendscount' => 1,
            'dashboard.getactivity' => 1,
            'dashboard.getcount' => 1,
            'dashboard.getglobalnews' => 1,
            'dashboard.getnews' => 1,
            'dashboard.multigetcount' => 1,
            'dashboard.multigetnews' => 1,
            'data.getcookies' => 1,
            'events.get' => 1,
            'events.getmembers' => 1,
            'fbml.getcustomtags' => 1,
            'feed.getappfriendstories' => 1,
            'feed.getregisteredtemplatebundlebyid' => 1,
            'feed.getregisteredtemplatebundles' => 1,
            'fql.multiquery' => 1,
            'fql.query' => 1,
            'friends.arefriends' => 1,
            'friends.get' => 1,
            'friends.getappusers' => 1,
            'friends.getlists' => 1,
            'friends.getmutualfriends' => 1,
            'gifts.get' => 1,
            'groups.get' => 1,
            'groups.getmembers' => 1,
            'intl.gettranslations' => 1,
            'links.get' => 1,
            'notes.get' => 1,
            'notifications.get' => 1,
            'pages.getinfo' => 1,
            'pages.isadmin' => 1,
            'pages.isappadded' => 1,
            'pages.isfan' => 1,
            'permissions.checkavailableapiaccess' => 1,
            'permissions.checkgrantedapiaccess' => 1,
            'photos.get' => 1,
            'photos.getalbums' => 1,
            'photos.gettags' => 1,
            'profile.getinfo' => 1,
            'profile.getinfooptions' => 1,
            'stream.get' => 1,
            'stream.getcomments' => 1,
            'stream.getfilters' => 1,
            'users.getinfo' => 1,
            'users.getloggedinuser' => 1,
            'users.getstandardinfo' => 1,
            'users.hasapppermission' => 1,
            'users.isappuser' => 1,
            'users.isverified' => 1,
            'video.getuploadlimits' => 1);
    $name = 'api';
    if (isset($READ_ONLY_CALLS[strtolower($method)])) {
      $name = 'api_read';
    }
    return self::getUrl($name, 'restserver.php');
  }

  /**
   * Build the URL for given domain alias, path and parameters.
   *
   * @param $name String the name of the domain
   * @param $path String optional path (without a leading slash)
   * @param $params Array optional query parameters
   * @return String the URL for the given parameters
   */
  protected function getUrl($name, $path='', $params=array()) {
    $url = self::$DOMAIN_MAP[$name];
    if ($path) {
      if ($path[0] === '/') {
        $path = substr($path, 1);
      }
      $url .= $path;
    }
    if ($params) {
      $url .= '?' . http_build_query($params, null, '&');
    }
    return $url;
  }

  /**
   * Returns the Current URL, stripping it of known FB parameters that should
   * not persist.
   *
   * @return String the current URL
   */
  protected function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
      ? 'https://'
      : 'http://';
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parts = parse_url($currentUrl);

    // drop known fb params
    $query = '';
    if (!empty($parts['query'])) {
      $params = array();
      parse_str($parts['query'], $params);
      foreach(self::$DROP_QUERY_PARAMS as $key) {
        unset($params[$key]);
      }
      if (!empty($params)) {
        $query = '?' . http_build_query($params, null, '&');
      }
    }

    // use port if non default
    $port =
      isset($parts['port']) &&
      (($protocol === 'http://' && $parts['port'] !== 80) ||
       ($protocol === 'https://' && $parts['port'] !== 443))
      ? ':' . $parts['port'] : '';

    // rebuild
    return $protocol . $parts['host'] . $port . $parts['path'] . $query;
  }

  /**
   * Generate a signature for the given params and secret.
   *
   * @param Array $params the parameters to sign
   * @param String $secret the secret to sign with
   * @return String the generated signature
   */
  protected static function generateSignature($params, $secret) {
    // work with sorted data
    ksort($params);

    // generate the base string
    $base_string = '';
    foreach($params as $key => $value) {
      $base_string .= $key . '=' . $value;
    }
    $base_string .= $secret;

    return md5($base_string);
  }

  /**
   * Prints to the error log if you aren't in command line mode.
   *
   * @param String log message
   */
  protected static function errorLog($msg) {
    // disable error log if we are running in a CLI environment
    // @codeCoverageIgnoreStart
    if (php_sapi_name() != 'cli') {
      error_log($msg);
    }
    // uncomment this if you want to see the errors on the page
    // print 'error_log: '.$msg."\n";
    // @codeCoverageIgnoreEnd
  }

  /**
   * Base64 encoding that doesn't need to be urlencode()ed.
   * Exactly the same as base64_encode except it uses
   *   - instead of +
   *   _ instead of /
   *
   * @param String base64UrlEncodeded string
   */
  protected static function base64UrlDecode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
  }

}

