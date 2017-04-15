<?php

/**
 * Contains TriggerNotification class
 *
 * @package Notifications
 */

/**
 * Trigger notification
 *
 * Notification for specified sensor events.
 *
 * @package Notifications
 */

class TriggerNotification extends Notification {

  /*
   * Time
   *
   * Time span when the notification is enabled.
   */
  private $time;
  /*
   * Default time
   */
  private $default_time = array(8, 18);
  /*
   * Sensor
   *
   * The sensor to listen on.
   */
  private $sensor;
  /*
   * Default sensor
   */
  private $default_sensor = 1;
  /*
   * Mode
   *
   * Whether to trigger on going below or above the value.
   */
  private $mode;
  /*
   * Default mode
   */
  private $default_mode = "below";
  /*
   * Bound
   *
   * Which value should trigger a notification.
   */
  private $bound;
  /*
   * Default bound
   */
  private $default_bound = 30;

  /*
   * Fetches time from the database
   */
  function get_time() {
    if (!$this->time) {
      $this->time = $this->default_time;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_trigger_time"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_trigger_time", "'.serialize($this->default_time).'")');
      else {
        $this->time = unserialize($result[0]['config_value']);
      }
    }
    return $this->time;
  }

  /**
   * Saves the time
   */
  function set_time($time) {
    DB::query('UPDATE uvr2web_config SET config_value="' . serialize($time) . '" WHERE config_key="notification_trigger_time"');
    $this->time = $time;
  }

  /*
   * Fetches sensor from the database
   */
  function get_sensor() {
    if (!$this->sensor) {
      $this->sensor = $this->default_sensor;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_trigger_sensor"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_trigger_sensor", "'.$this->default_sensor.'")');
      else {
        $this->sensor = (int) $result[0]['config_value'];
      }
    }
    return $this->sensor;
  }

  /**
   * Saves the sensor
   */
  function set_sensor($sensor) {
    DB::query('UPDATE uvr2web_config SET config_value="' . (int) $sensor . '" WHERE config_key="notification_trigger_sensor"');
    $this->sensor = (int) $sensor;
  }

  /*
   * Fetches mode from the database
   */
  function get_mode() {
    if (!$this->mode) {
      $this->mode = $this->default_mode;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_trigger_mode"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_trigger_mode", "'.$this->default_mode.'")');
      else {
        $this->mode = $result[0]['config_value'];
      }
    }
    return $this->mode;
  }

  /**
   * Saves the mode
   */
  function set_mode($mode) {
    if ($mode !== "below" && $mode !== "above")
      return;
    DB::query('UPDATE uvr2web_config SET config_value="' . $mode . '" WHERE config_key="notification_trigger_mode"');
    $this->mode = $mode;
  }

  /*
   * Fetches bound from the database
   */
  function get_bound() {
    if (!$this->bound) {
      $this->bound = $this->default_bound;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_trigger_bound"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_trigger_bound", "'.$this->default_bound.'")');
      else {
        $this->bound = (int) $result[0]['config_value'];
      }
    }
    return $this->bound;
  }

  /**
   * Saves the bound
   */
  function set_bound($bound) {
    DB::query('UPDATE uvr2web_config SET config_value="' . (int) $bound . '" WHERE config_key="notification_trigger_bound"');
    $this->bound = (int) $bound;
  }

  function check() {
    $data = DataFrame::open();
    $value = (new Sensor($this->get_sensor()))->fetch_by($data);
    $current_hour = (int) date('H');
    $is_active = $current_hour >= $this->get_time()[0] && $current_hour <= $this->get_time()[1];
    $bound = $this->get_bound();
    $under_bound = $value <= 0.95 * $bound;
    $over_bound = $value >= 1.05 * $bound;
    $trigger_started = $this->get_mode() === "below" ? $under_bound : $over_bound;
    $trigger_ended = $this->get_mode() === "below" ? $over_bound : $under_bound;

    if ($trigger_started && $this->get_sent() == 'false')
      return $is_active;
    elseif ($trigger_ended && $this->get_sent() != 'false')
      $this->set_sent('false');
    
    return false;
  }

  function render() {
    $script = dirname($_SERVER['SCRIPT_NAME']) . 'index.php';
    $link = "http://$_SERVER[SERVER_NAME]$script?p=admin&sub=notifications";
    $notification = Loc::t('notification');
    $sensor = new Sensor($this->get_sensor());
    $sensor_alias = $sensor->get_alias();
    $mode = $this->get_mode();
    $bound = $this->get_bound();
    $data = DataFrame::open();
    $value = $sensor->fetch_by($data);
    $footer1 = Loc::t('notification footer');
    $footer2 = Loc::t('notification footer 2');
    $footer3 = Loc::t('notification footer 3');
    $footer4 = Loc::t('notification footer 4');
    $message = <<<code
    <html>
    <head>
    <title>
    $notification: Trigger
    </title>
    </head>
    <body>
    <div style="width:800px;margin:20px auto;font-family:Arial">
    <div style="border:1px solid black;padding:20px;background-color:#eee">
    <h1 style="margin-top:10px;border-bottom:1px solid #999">$notification</h1>
    <h2 style="font-size:1.8em">Trigger</h2>
    <p style="font-size:16px;line-height:1.8em;padding:0 0 10px 0">
      <b>$sensor_alias</b> is now $mode the value <b>$bound</b>. The current value is <b>$value</b>.
    </p>
    </div>
    <small style="display:block;margin-top:10px;text-align:center">$footer1 <a href="$link">$footer2</a>$footer3<br />
    $footer4</small> 
    </div>
    </body>
    </html>
code;
    return array('subject' => "$notification: Trigger", 'message' => $message);
  }

}

?>