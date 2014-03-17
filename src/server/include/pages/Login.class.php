<?php

/**
 * Contains Login class
 *
 * @package Pages
 */
 
/**
 * Renders the login page
 *
 * Renders the login page if unauthorized.
 *
 * @package Pages
 */

class Login {
  
  /**
   * Login fail
   */
  public static $fail = false;

  /**
   * Gets the page title
   */
  public function title() {
    return 'Login';
  }
  
  /**
   * Renders the login page
   */
  public function render() {
    if (self::$fail)
      $fail = '<div class="login_fail">' . Loc::t('login incorrect') . '</div>';
    else
      $fail = '';
    $field = stristr($_SERVER['HTTP_USER_AGENT'], 'Android') ? 'text' : 'password';
    $nav = Renderer::$nav;
    $login = Loc::t('log in');
    echo <<<code
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="css/style.css" rel="stylesheet" media="screen" />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/highstock.js"></script>
    <script type="text/javascript" src="js/exporting.js"></script>
    <script type="text/javascript" src="js/turbolinks.min.js"></script>
		<title>
			$login | uvr2web
		</title>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
	</head>
	<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div id="loading"></div>
          <a class="brand" href="#">uvr2web</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              $nav
            </ul>
          </div>
        </div>
      </div>
    </div>
	<div class="container">
	<div class="row">
    <form class="login" method="post" action="$_SERVER[PHP_SELF]">
    <div>
        <input type="text" name="username" class="input-block-level" />
        <input type="$field" name="password" class="input-block-level" />
        <button class="btn btn-large btn-primary" type="submit">$login</button>
        $fail
        <div class="clear"></div>
        </div>
      </form>
      </div>
    </div>
    </body>
    </html>
code;
  }
  
}

?>