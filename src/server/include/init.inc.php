<?php

/**
 * Initializes uvr2web
 *
 * Also contains important configuration values.
 *
 * @package default
 */
 
// ******************************** adjust the following *********************************

// remove the comment for production mode (no errors)
$mode = 'production';

// remove the comment for debug mode (all errors)
$mode = 'debug';

// remove the comment for links to documentation and information on uvr2web
$mode .= ' meta';

// adjust if you are in another timezone, see http://www.php.net/manual/de/timezones.php
date_default_timezone_set('Europe/Berlin');

// ****************************************************************************************

if (stristr($mode, 'debug'))
  error_reporting(-1);
if (stristr($mode, 'production'))
  error_reporting(0);
if (stristr($mode, 'meta'))
  $GLOBALS['meta_nav'] = true;
else
  $GLOBALS['meta_nav'] = false;

require_once 'Config.class.php';
require_once 'Loc.class.php';
require_once 'DB.class.php';
require_once 'Renderer.class.php';
require_once 'DeviceRenderer.class.php';
require_once 'AdminRenderer.class.php';
require_once 'highcharts/Highchart.php';
require_once 'dataframe/Device.class.php';
require_once 'dataframe/DataFrame.class.php';
require_once 'notifications/Notifier.class.php';

try {
  $GLOBALS['cfg'] = Config::get_config();
} catch (Exception $e) {
  $GLOBALS['cfg']['active'] = 'false';
}

$header = Loc::t('header');
$footer = Loc::t('footer');
if ($GLOBALS['cfg']['active'] != 'true') {
  $code = <<<code
  $header
	<p style="margin:30px">uvr2web is currently disabled.</p>
	$footer
code;
  die($code);
}
if (file_exists('install.php')) {
  $code = <<<code
  $header
	<p>To finish the installation, you have to delete <em>install.php</em> with your FTP client.</p>
  <p><a href="index.php">I deleted <em>install.php</em></a>.</p>
	$footer
code;
  die($code);
}

$GLOBALS['pass']            = $GLOBALS['cfg']['upload_password'];
$GLOBALS['db_frame']        = $GLOBALS['cfg']['data_record_interval'];
$GLOBALS['upload_interval'] = $GLOBALS['cfg']['upload_interval'];

DB::start($GLOBALS['cfg']['server'], $GLOBALS['cfg']['username'], $GLOBALS['cfg']['password'], $GLOBALS['cfg']['database']);
DB::connect();

Loc::init();

$data = DataFrame::open();
HeatMeter::detect_order($data);
SpeedStep::detect_order($data);

Notifier::notify();

?>