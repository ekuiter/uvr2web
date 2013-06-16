<?php

/**
 * Contains NoUploadNotification class
 *
 * @package Notifications
 */

/**
 * No upload notification
 *
 * Notification for upload issues.
 *
 * @package Notifications
 */

class NoUploadNotification extends Notification {

  /*
   * Time
   *
   * Time in minutes after that notification is sent.
   */
  private $time;
  /*
   * Default time
   */
  private $default_time = 120;
  /*
   * Minimal time
   */
  private $minimal_time = 10;

  /*
   * Fetches time from the database
   */
  function get_time() {
    if (!$this->time) {
      $this->time = $this->default_time;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_no_upload_time"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_no_upload_time", "'.$this->default_time.'")');
      else {
        $this->time = (int) $result[0]['config_value'];
      }
    }
    return $this->time;
  }

  /**
   * Saves the time
   */
  function set_time($time) {
    $time = (int) $time < 10 ? 10 : (int) $time;
    DB::query('UPDATE uvr2web_config SET config_value="' . $time . '" WHERE config_key="notification_no_upload_time"');
    $this->time = $time;
  }

  function check() {
    $result = DB::query('SELECT * FROM uvr2web_data ORDER BY timestamp DESC LIMIT 0,1');
    foreach ($result as $row) {
      $last_data_frame = (time() - strtotime($row['timestamp'])) / 60;
      if ($last_data_frame > $this->get_time() && $this->get_sent() == 'false')
        return true;
      elseif ($last_data_frame < $this->get_time() && $this->get_sent() != 'false')
        $this->set_sent('false');
    }
    return false;
  }

  function render() {
    $no_upload_time = $this->get_time();
    $result = DB::query('SELECT * FROM uvr2web_data ORDER BY timestamp DESC LIMIT 0,1');
    foreach ($result as $row) {
      $timestamp = Loc::mysql_timestamp($row['timestamp']);
      $timestamp['l'] = 'date';
      $date = Loc::l($timestamp);
      $timestamp['l'] = 'time';
      $time = Loc::l($timestamp);
    }
    $script = dirname($_SERVER['SCRIPT_NAME']) . 'index.php';
    $link = "http://$_SERVER[SERVER_NAME]$script?p=admin&sub=notifications";
    $upload_issues = Loc::t('upload issues');
    $notification = Loc::t('notification');
    $body1 = Loc::t('no upload notification body');
    $body2 = Loc::t('no upload notification body 2');
    $body3 = Loc::t('no upload notification body 3');
    $body4 = Loc::t('no upload notification body 4');
    $body5 = Loc::t('no upload notification body 5');
    $footer1 = Loc::t('notification footer');
    $footer2 = Loc::t('notification footer 2');
    $footer3 = Loc::t('notification footer 3');
    $footer4 = Loc::t('notification footer 4');
    $message = <<<code
    <html>
    <head>
    <title>
    $notification: $upload_issues
    </title>
    </head>
    <body>
    <div style="width:800px;margin:20px auto;font-family:Arial">
    <div style="border:1px solid black;padding:20px;background-color:#eee">
    <h1 style="margin-top:10px;border-bottom:1px solid #999">$notification</h1>
    <h2 style="font-size:1.8em">$upload_issues</h2>
    <p style="font-size:16px;line-height:1.8em;padding:0 0 10px 0">
    $body1<br />
    $body2 <strong>$no_upload_time</strong> $body3 <strong>$date</strong> $body4 <strong>$time</strong>).<br />
    $body5
    </p>
    </div> 
    <small style="display:block;margin-top:10px;text-align:center">$footer1 <a href="$link">$footer2</a>$footer3<br />
    $footer4</small>   
    </div>
    </body>
    </html>
code;
    return array('subject' => "$notification: $upload_issues", 'message' => $message);
  }

}

?>