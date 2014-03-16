<?php

/**
 * Contains Notifier class
 *
 * @package Notifications
 */

require_once dirname(__FILE__).'/Notification.class.php';
require_once dirname(__FILE__).'/NoUploadNotification.class.php';
require_once dirname(__FILE__).'/BackupNotification.class.php';

/**
 * Notifier
 *
 * Provides notification management.
 *
 * @package Notifications
 */

class Notifier {

  /**
   * Notifier email addresses
   *
   * Notifier sends notifications to this email addresses. Multiple emails are separated by comma.
   */
  private static $emails;
  private static $notifications = array('NoUploadNotification', 'BackupNotification');

  /**
   * Fetches all email addresses from the database
   */
  static function get_emails() {
    if (!self::$emails) {
      self::$emails = array();
      $result = DB::query('SELECT * FROM uvr2web_config WHERE config_key="notifier_emails"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("notifier_emails", "a:0:{}")');
      else {
        $emails = unserialize($result[0]['config_value']);
        foreach ($emails as &$email) {
          $email = trim($email);
          if (filter_var($email, FILTER_VALIDATE_EMAIL))
            self::$emails[] = $email;
        }
      }
    }
    return self::$emails;
  }

  /**
   * Returns a joined email address string
   */
  static function get_emails_string() {
    $emails = self::get_emails();
    return implode(', ', $emails);
  }

  /**
   * Saves an email address string
   */
  static function set_emails_string($emails_string) {
    $emails = explode(',', $emails_string);
    self::set_emails($emails);
  }

  /**
   * Saves an email address array
   */
  static function set_emails($emails) {
    $filtered_emails = array();
    foreach ($emails as &$email) {
      $email = trim($email);
      if (filter_var($email, FILTER_VALIDATE_EMAIL))
        $filtered_emails[] = $email;
    }
    DB::query('UPDATE uvr2web_config SET config_value="' . DB::escape(serialize($filtered_emails)) . '" WHERE config_key="notifier_emails"');
    self::$emails = $filtered_emails;
  }

  /*
   * Checks and renders notifications
   */
  static function notify() {
    foreach (self::$notifications as $notification) {
      $notification = new $notification();
      if ($notification->get_enabled() && $notification->check()) {
        $data = $notification->render();
        self::send($data);
        $notification->set_sent(time());
      }
    }
  }

  /*
   * Sends notifications
   */
  static function send($data) {
    foreach (self::get_emails() as $email) {
      mail($email, $data['subject'], $data['message'], "From: uvr2web <notifications@$_SERVER[SERVER_NAME]>\r\nContent-type: text/html; charset=utf-8");
    }
  }

}

?>