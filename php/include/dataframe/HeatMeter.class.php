<?php

/**
 * Contains HeatMeter class
 *
 * @package Devices
 */
 
/**
 * Heat meter device
 *
 * Provides heat meter handling.
 *
 * @package Devices
 */

class HeatMeter extends Device {

  /**
   * Static device type
  */
  private static $stype = 'heat_meter';
  /**
   * Device type 
  */
  protected $type = 'heat_meter';
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
  protected $type_plural = 'heat_meters';
  /**
   * Default device order 
  */
  private static $default_order = array();
  /**
   * Key for heat meter value
  */
  private $key;
  /**
   * Default device alias 
  */
  protected $default_alias;
  /**
   * Number
  */
  public $number = '';
  
  /**
   * Detects the right order
   * @param array $data_frame
   */
  public static function detect_order($data_frame) {
    if (isset($data_frame['heat_meters'][0])) {
      self::$default_order = array(array(1, 2, 3));
      if (isset($data_frame['heat_meters'][1]))
        self::$default_order = array(array(1, 2, 3), array(4, 5, 6));
    }
  }
  
  /**
   * Detects the right key
   */
  protected function init() {
    switch ($this->no % 3) {
    case 1:
      $this->key = 'current_power';
      $this->default_alias = Loc::t('current power');
      break;
    case 2:
      $this->key = 'kwh';
      $this->default_alias = Loc::t('kwh');
      break;
    case 0:
      $this->key = 'mwh';
      $this->default_alias = Loc::t('mwh');
      break;
    }
  } 

  /**
   * Checks device number's correctness
   * @param int $no
   */
  protected function check_no($no) {
    if (!is_numeric($no) || is_float($no) || $no < 1 || $no > 6)
      return false;
    return $no;
  }

  /**
   * Gets device
   */
  public static function get() {    
    $order = parent::get_order_by(self::$stype, self::$default_order);
    return parent::get_by('HeatMeter', $order);
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
    if (!isset ($data_frame['heat_meters']))
      return null;
    return $data_frame['heat_meters'][ceil(($this->no / 3) - 1)][$this->key];
  }

  /**
   * Renders device as a preview box (mini graph)
   */
  public function render_box() {
    $alias = $this->get_alias();
    $data = DataFrame::open();
    $value = Loc::l($this->fetch_by($data));
    $unit = $this->key == 'current_power' ? 'kW' : ($this->key == 'kwh' ? 'kWh' : 'MWh');
    echo <<<code
     <div class="box" id="heat_meter$this->no">
      <a href="?p=heat_meters&no=$this->no">
        <div class="inner">
          $alias
          <div class="value">$value $unit</div>
        </div>
        <img src="?p=heat_meters&image&no=$this->no&size=$_GET[size]" alt="Heat meter $this->no Graph" />
      </a>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
      $("#heat_meters #heat_meter$this->no").hover(
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
    $unit = $this->key == 'current_power' ? 'kW' : ($this->key == 'kwh' ? 'kWh' : 'MWh');
    $this->extreme_values($unit);
  }

}

?>