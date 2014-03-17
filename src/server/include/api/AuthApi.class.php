<?php

/**
 * Contains AuthApi class
 *
 * @package Api
 */

/**
 * Auth API
 *
 * Login and logout.
 *
 * @package Api
 */

class AuthApi {
  
  function login($username, $password) {
    if (ApiHelper::logged_in())
      throw new Exception('already logged in');
    else {
      $result = DB::query('SELECT * FROM uvr2web_users WHERE username="' . DB::escape($username) . '"');
      if ($result && $result[0]['password'] == md5($password)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $result[0]['username'];
        $_SESSION['role'] = $result[0]['role'];
        return "logged in as '$_SESSION[username]'";
      } else
        throw new Exception('login incorrect');
    }
  }
  
  function logout() {
    ApiHelper::authenticate();
    session_destroy();
    return 'logged out';
  }
  
}
  
?>