<?php

/**
 * Contains Notification class
 *
 * @package Notifications
 */

/**
 * Generic notification
 *
 * Generic notification sent as email.
 *
 * @package Notifications
 */

abstract class Notification {

  /*
   * Notification name
   */
  protected $name;
  /*
   * Enabled flag
   *
   * Whether the notification is enabled or not.
   */
  protected $enabled;
  /*
   * Sent flag
   *
   * Contains sending information.
   */
  protected $sent;
  /*
   * Checks if the notification should be sent
   */
  abstract function check();
  /*
   * Renders the notification
   */
  abstract function render();

  /**
   * Checks if notification is enabled
   */
  function get_enabled() {
    $this->get_name();
    if (!$this->enabled) {
      $this->enabled = false;
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_'.$this->get_name().'_enabled"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_'.$this->get_name().'_enabled", "0")');
      else
        $this->enabled = $result[0]['config_value'] == '0' ? false : true;
    }
    return $this->enabled;
  }
  
  /**
   * Saves the enabled flag
   */
  function set_enabled($enabled) {
    DB::query('UPDATE uvr2web_config SET config_value="' . ($enabled ? 1 : 0) . '" WHERE config_key="notification_'.$this->get_name().'_enabled"');
    $this->enabled = $enabled;
  }
  
  /**
   * Fetches the sent flag
   */
  function get_sent() {
    $this->get_name();
    if (!$this->sent) {
      $this->sent = 'false';
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notification_'.$this->get_name().'_sent"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notification_'.$this->get_name().'_sent", "false")');
      else
        $this->sent = $result[0]['config_value'];
    }
    return $this->sent;
  }
  
  /**
   * Saves the sent flag
   */
  function set_sent($sent) {
    DB::query('UPDATE uvr2web_config SET config_value="'.DB::escape($sent).'" WHERE config_key="notification_'.$this->get_name().'_sent"');
    $this->sent = $sent;
  }
  
  /*
   * Gets the notification name
   */
  function get_name() {
    if (!$this->name) {
      $class = get_class($this);
      $class = str_replace('Notification', '', $class);
      $class = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $class);
      $class = strtolower($class);
      $this->name = $class;
    }
    return $this->name;
  }

}

?>