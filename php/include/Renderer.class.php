<?php

/**
 * Contains Renderer class
 *
 * @package Renderers
 */

/**
 * Renders the web page
 *
 * Renders header, body and footer of the web page. Manages authentication, sessions, navigation etc.
 *
 * @package Renderers
 */

class Renderer {

  /**
   * Default page
   */
  private $page = 'sensors';
  /**
   * Renderer object
   */
  private $obj;
  /**
   * Navigation bar
   */
  public static $nav = '';
  /**
   * Live request
   */
  private $live = false;
  /**
   * Image request
   */
  private $image = false;
  /*
   * Whether page should be shown
   */
  private $show_page;

  /**
   * Processes a request
   */
  public function __construct() {
    session_start();
    if (isset($_POST['username']) && isset($_POST['password']))
      $this->log_in();
    else if ($GLOBALS['meta'] && $_SERVER['QUERY_STRING'] == 'demo')
      $this->log_in_guest();
    $this->get_page();
    if ($this->page == 'Login') {
      if ($GLOBALS['meta'])
        $this->get_meta_nav();
      $this->body();
    } else if (isset($_GET['live']) && method_exists($this->obj, 'live')) {
        $this->live = true;
        $this->body();
      } else if (isset($_GET['image']) && method_exists($this->obj, 'image')) {
        $this->image = true;
        $this->body();
      } else {
      $this->get_nav();
      if ($GLOBALS['meta'])
        $this->get_meta_nav();
      $this->show_page();
      $this->header();
      $this->body();
      $this->footer();
    }
  }

  /**
   * Login if unauthenticated
   */
  private function log_in() {
    $result = DB::query('SELECT * FROM uvr2web_users WHERE username="' . DB::escape($_POST['username']) . '"');
    if ($result && (new Password($_POST['password']))->verify($result[0]['password'])) {
      $_SESSION['logged_in'] = true;
      $_SESSION['username'] = $result[0]['username'];
      $_SESSION['role'] = $result[0]['role'];
      header('Location: ' . dirname($_SERVER["PHP_SELF"]));
    } else {
      require_once dirname(__FILE__).'/pages/Login.class.php';
      Login::$fail = true;
    }
  }
  
  /**
   * Login as guest
   */
  private function log_in_guest() {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = 'demo';
    $_SESSION['role'] = 'user';
    header('Location: ' . dirname($_SERVER["PHP_SELF"]));
  }

  /**
   * Logout if desired
   */
  private function log_out() {
    session_destroy();
    header('Location: ' . dirname($_SERVER["PHP_SELF"]));
  }

  /**
   * Determines the requested page
   */
  private function get_page() {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'])
      $this->page = 'login';
    else if (isset($_GET['p']))
        $this->page = $_GET['p'];
      $this->page = preg_replace('/[^a-zA-Z0-9_]+/', '', $this->page);
    $this->page = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->page)));
    if ($this->page == 'Logout')
      $this->log_out();
    $this->include_page();
    if (isset($this->obj->role) && $this->obj->role == 'admin' && $_SESSION['role'] != 'admin') {
      $this->page = 'NotFound';
      $this->include_page();
    }
  }

  /**
   * Creates a renderer object
   */
  private function include_page() {
    if (!is_file(dirname(__FILE__).'/pages/' . $this->page . '.class.php'))
      $this->page = 'NotFound';
    require_once dirname(__FILE__).'/pages/' . $this->page . '.class.php';
    $this->obj = new $this->page();
  }

  /**
   * Gets the navigation bar
   */
  private function get_nav() {
    $this->add_link(Loc::t('sensors'), 'sensors');
    $this->add_link(Loc::t('outputs'), 'outputs');
    $this->add_link(Loc::t('heat meters'), 'heat_meters');
    $this->add_link(Loc::t('speed steps'), 'speed_steps');
    if ($_SESSION['role'] == 'admin') {
      $this->add_link(Loc::t('status'), 'status');
      $this->add_link(Loc::t('admin'), 'admin');
    }
    $this->add_link(Loc::t('logout'), 'logout');
  }

  /**
   * Gets an additional meta navigation
   */
  private function get_meta_nav() {
    $this->add_link_extern(Loc::t('about'), 'http://elias-kuiter.de/apps/uvr2web');
    $this->add_link_extern(Loc::t('docs'), 'http://ekuiter.github.io/uvr2web/');
  }

  /**
   * Adds a link to the navigation bar
   * @param string $title
   * @param string $page
   * @param mixed  $params
   */
  private function add_link($title, $page, $params = null) {
    self::$nav .= '<li' . ($this->page == str_replace(' ', '', ucwords(str_replace('_', ' ', $page))) ? ' class="active"' : '') .
      "><a href=\"?p=$page" . ($params ? "&$params" : '') . "\">$title</a></li>";
  }

  /**
   * Adds a link to an external website to the navigation bar
   * @param string $title
   * @param string $url
   * @param string $options
   */
  private function add_link_extern($title, $url, $options = '') {
    self::$nav .= "<li$options><a href=\"$url\" target=\"_blank\">$title</a></li>";
  }

  /**
   * Adds a link to the sidebar
   * @param string $title
   * @param string $url
   * @param bool   $active
   */
  public static function sidebar_link($title, $url, $active = false) {
    echo '<li' . ($active ? ' class="active"' : '') .
      "><a href=\"$url\">$title</a></li>";
  }

  /**
   * Renders the website header
   */
  private function header() {
    $title = $this->obj->title();
    $nav = self::$nav;
    echo <<<code
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="assets/vendor.css" rel="stylesheet" />
		<link href="assets/style.css" rel="stylesheet" media="screen" />
		<script type="text/javascript" src="assets/vendor.js"></script>
		<title>
			$title | uvr2web
		</title>
		<link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
		<link rel="icon" href="assets/favicon.ico" type="image/x-icon">
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
code;
    if (method_exists($this->obj, 'sidebar') && ($this->show_page || $this->page == 'Admin')) {
      echo <<<code
    <div class="span2">
      <ul class="nav nav-stacked nav-pills sidebar">
code;
      $this->obj->sidebar();
      echo <<<code
    	</ul>
    </div>
    <div class="span10">
code;
    } else {
      echo '<div class="span12">';
    }
  }

  /**
   * Renders the website body
   */
  private function body() {  
    if ($this->live)
      $this->obj->live();
    else if ($this->image)
        $this->obj->image();
      else {
        if ($this->page != 'Login')
          echo "\n<h1>" . $this->obj->title() . '</h1>';
        if ($this->show_page || $this->page == 'Admin' || $this->page == 'Login')          
          $this->obj->render();
        else
          $this->show_warning();
      }
  }
  
  private function show_page() {
    $result = DB::query('SELECT * FROM uvr2web_data LIMIT 0,1');
    $this->show_page = count($result) ? true : false;
  }
  
  private function show_warning() {
    $interval = $GLOBALS['cfg']['upload_interval'] / 1000;
    $no_data_frames = Loc::t('no data frames');
    $no_data_frames_2 = Loc::t('no data frames 2');
    echo <<<code
      <div class="alert alert-error alert-block">
        $no_data_frames $interval $no_data_frames_2
      </div>
code;
  }

  /**
   * Renders the website footer
   */
  private function footer() {
    echo <<<code
    </div>
    </div>
    </div>
    </body>
    </html>
code;
  }

}

?>