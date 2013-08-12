<?php

/**
 * Contains Config class
 *
 * @package Config
 */

/**
 * uvr2web configuration
 *
 * Manages the configuration file.
 *
 * @package Config
 */

class Config {

  /*
   * Path to the configuration file
   */
  static $config_file = 'include/cfg';
  /*
   * Default configuration
   */
  static $default_config = array('active' => 'false');

  /*
   * Reads configuration file
   */
  public static function get_config() {
    if (file_exists(self::$config_file))
      return unserialize(file_get_contents(self::$config_file));
    else
      return self::set_config(self::$default_config);
  }

  /*
   * Writes configuration file
   */
  public static function set_config($config) {
    if (@file_put_contents(self::$config_file, serialize($config)) === false)
      throw new Exception(dirname(self::$config_file).' is not writeable');
    return $config;
  }
  
  /*
   * Deletes configuration file and directory
   */
  public static function delete_config() {
    if (file_exists(self::$config_file))
      unlink(self::$config_file);
  }

}

?>