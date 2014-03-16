<?php

/**
 * Contains DataFrame class
 *
 * @package DataFrame
 */

require_once dirname(__FILE__).'/Uploader.class.php';
require_once dirname(__FILE__).'/FrameCounter.class.php';

/**
 * Data frame processing
 *
 * Reads, writes and analyzes data frames. 
 *
 * @package DataFrame
 */

class DataFrame {

  /**
   * Contains a raw uploaded data frame 
  */
  private $raw = array();
  /**
   * Contains an uploaded data frame 
  */
  private $data = array();
  /**
   * Data frame array pointer
  */
  private $pointer = 0;
  /**
   * File path for latest data frame
  */
  public static $file;
  
  /**
   * Initializes the DataFrame class
   */
  public static function init() {
    self::$file = dirname(__FILE__).'/../uvr';
  }

  /**
   * Processes the uploading data frame
   * @param string $raw
  */
  public function __construct($raw) {
    $raw = str_replace($GLOBALS['pass'] . '&', '', $raw);
    $this->raw = split('&', $raw);
    foreach ($this->raw as &$entry)
      if ($entry != '-')
        $entry = (int) $entry;
      $this->sensors();
    $this->heat_meters();
    $this->outputs();
    $this->speed_steps();
  }

  /**
   * Opens the latest data frame
  */
  public static function open() {
    if (!is_file(self::$file))
      return array();
    return unserialize(file_get_contents(self::$file));
  }

  /**
   * Saves the uploaded data frame
  */
  public function save() {
  if (@file_put_contents(self::$file, serialize($this->data)) === false)
      throw new Exception(dirname(self::$file).' is not writeable');
  }
  
  public static function delete() {
    if (file_exists(self::$file))
      unlink(self::$file);
  }
  
  /**
   * Saves the uploaded data frame into the database
  */
  public function save_to_db() {
    DB::query('INSERT INTO uvr2web_data (timestamp, data_frame)
              VALUES("' . DB::escape(date('Y-m-d H:i:s')) . '", "' . 
              DB::escape(serialize($this->data)) . '")');
  }

  /**
   * Returns the last upload time
   **/
  public function last_upload() {
    return filemtime(self::$file);
  }

  /**
   * Reads sensor data from raw data frame 
  */
  private function sensors() {
    for ($i = 0; $i < 16; $i++) {
      $sensor = array();
      switch ($this->raw[$i * 4 + 2]) {
      case 0:
        $type = 'unused';
        break;
      case 1:
        $type = 'digital';
        break;
      case 2:
        $type = 'temperature';
        break;
      case 3:
        $type = 'volume flow';
        break;
      case 6:
        $type = 'rays';
        break;
      case 7:
        $type = 'room temperature';
        break;
      }
      $sensor['type'] = $type;
      if ($type === 'room temperature') {
        switch ($this->raw[$i * 4 + 3]) {
        case 0:
          $mode = 'auto';
          break;
        case 1:
          $mode = 'normal';
          break;
        case 2:
          $mode = 'lower';
          break;
        case 3:
          $mode = 'standby';
          break;
        }
        $sensor['mode'] = $mode;
      }
      $sensor['value'] = $this->raw[$i * 4 + 1] / 10;
      $this->data['sensors'][$i] = $sensor;
    }
  }

  /**
   * Reads heat meter data from raw data frame 
  */
  private function heat_meters() {
    $hm1 = array();
    $hm2 = array();
    if ($this->raw[65] === '-') {
      if ($this->raw[67] === '-') {
        $this->pointer = 68;
      } else {
        $hm2['current_power'] = $this->raw[67] / 100;
        $hm2['kwh'] = $this->raw[68] / 10;
        $hm2['mwh'] = $this->raw[69];
        $this->data['heat_meters'][0] = $hm2;
        $this->pointer = 70;
      }
    } else {
      $hm1['current_power'] = $this->raw[65] / 100;
      $hm1['kwh'] = $this->raw[66] / 10;
      $hm1['mwh'] = $this->raw[67];
      $this->data['heat_meters'][0] = $hm1;
      if ($this->raw[69] === '-') {
        $this->pointer = 70;
      } else {
        $hm2['current_power'] = $this->raw[69] / 100;
        $hm2['kwh'] = $this->raw[70] / 10;
        $hm2['mwh'] = $this->raw[71];
        $this->data['heat_meters'][1] = $hm2;
        $this->pointer = 72;
      }
    }
  }

  /**
   * Reads output data from raw data frame 
  */
  private function outputs() {
    for ($i = 0; $i < 13; $i++)
      $this->data['outputs'][$i] = $this->raw[$this->pointer + 1 + $i * 2];
    $this->pointer += 26;
  }

  /**
   * Reads speed step data from raw data frame 
  */
  private function speed_steps() {
    $outputs = array(0, 1, 5, 6);
    for ($i = 0; $i < 4; $i++) {
      $value = $this->raw[$this->pointer + 1 + $i * 2];
      if ($value !== '-')
        $this->data['speed_steps'][$i] = array('output' => $outputs[$i], 'value' => $value);
    }
  }

}

?>