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
  
  public $login = 'Log in to uvr2web using a username and a password.';
  public $login_ex = array('auth.login(my_user,my_pass)');
  public $logout = 'Log out (login required).';
  public $logout_ex = array('auth.logout');
  
  function login($username, $password) {
    if (ApiHelper::logged_in())
      return session_id();
    else {
      $result = DB::query('SELECT * FROM uvr2web_users WHERE username="' . DB::escape($username) . '"');
      if ($result && (new Password($password))->verify($result[0]['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $result[0]['username'];
        $_SESSION['role'] = $result[0]['role'];
        return session_id();
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