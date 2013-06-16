<?php

/**
 * Contains Sensor class
 *
 * @package Devices
 */
 
/**
 * Sensor device
 *
 * Provides sensor handling.
 *
 * @package Devices
 */

class Sensor extends Device {


  /**
   * Static device type
  */
  private static $stype = 'sensor';
  /**
   * Device type 
  */
  protected $type = 'sensor';
  /**
   * Readable device type 
  */
  protected $human_type;
  /**
   * Readable device type (plural) 
  */
  protected $human_type_plural;
  /**
   * Device type (plural) 
  */
  protected $type_plural = 'sensors';
  /**
   * Default device order 
  */
  private static $default_order = array(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16));

  /**
   * Checks device number's correctness
   * @param int $no
   */
  protected function check_no($no) {
    if (!is_numeric($no) || is_float($no) || $no < 1 || $no > 16)
      return false;
    return $no;
  }

  /**
   * Gets device
   */
  public static function get() {
    $order = parent::get_order_by(self::$stype, self::$default_order);
    return parent::get_by('Sensor', $order);
  }

  /**
   * Gets device order
   */
  public static function get_order() {
    return parent::get_order_by(self::$stype, self::$default_order);
  }

  /**
   * Sets device order
   * @param array $order
   */
  public static function set_order($order) {
    parent::set_order_by(self::$stype, $order);
  }

  /**
   * Fetches device data by data frame
   * @param array $data_frame
   */
  public function fetch_by($data_frame) {
    return $data_frame['sensors'][$this->no - 1]['value'];
  }

  /**
   * Renders device as a preview box (mini graph)
   */
  public function render_box() {
    $alias = $this->get_alias();
    $data = DataFrame::open();
    $sensor = $data['sensors'][$this->no - 1];
    $value = $sensor['value'];
    $unit = '';
    switch ($sensor['type']) {
    case 'unused':
      $value = '-';
      break;
    case 'digital':
      $value = $value ? 'On' : 'Off';
      break;
    case 'temperature':
      $unit = '°C';
      break;
    case 'volume flow':
      $unit = 'l/h';
      break;
    case 'rays':
      $unit = 'W/m²';
      break;
    case 'room temperature':
      switch ($sensor['mode']) {
      case 'auto':
        $mode = 'auto';
        break;
      case 'normal':
        $mode = 'normal';
        break;
      case 'lower':
        $mode = 'lower';
        break;
      case 'standby':
        $mode = 'standby';
        break;
      }
      $unit = "°C ($mode)";
      break;
    }
    $level = strstr($sensor['type'], 'temperature') ? ($value > 40 ? 'hot' : 'cold') : '';
    $value = Loc::l($value);
    echo <<<code
     <div class="box" id="sensor$this->no">
      <a href="?p=sensors&no=$this->no">
        <div class="inner">
          <div class="number">$this->no</div> $alias
          <div class="value"><span class="$level">$value</span> $unit</div>
        </div>
        <img src="?p=sensors&image&no=$this->no&size=$_GET[size]" alt="Sensor $this->no Graph" />
      </a>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
      $("#sensors #sensor$this->no").hover(
      function() {
        \$(this).find(".inner").hide();
        \$(this).find("img").fadeIn(60);
      },
      function() {
        \$(this).find("img").hide();
        \$(this).find(".inner").fadeIn(60);
      });
    /* ]]> */
    </script>
code;
  }

  /**
   * Prepares a mini graph
   * @param array $data
   * @param int   $x
   * @param int   $y
   */
  protected function image_data($data, $x, $y) {
    $min = min($data);
    $max = max($data);
    $mid = $y / 2;
    if ($min == $max) {
      foreach ($data as &$value)
        $value = $mid;
    } else {
      foreach ($data as &$value)
        $value = ($y - 1) - (int) (($value - $min) * $y / ($max - $min));
    }
    return array_reverse($data);
  }

  /**
   * Renders extreme values
   */
  protected function page_additional() {
    $unit = '';
    $data = DataFrame::open();
    $sensor = $data['sensors'][$this->no - 1];
    switch ($sensor['type']) {
    case 'temperature':
    case 'room temperature':
      $unit = '°C';
      break;
    case 'volume flow':
      $unit = 'l/h';
      break;
    case 'rays':
      $unit = 'W/m²';
      break;
    }
    $this->extreme_values($unit);
  }

}

?>