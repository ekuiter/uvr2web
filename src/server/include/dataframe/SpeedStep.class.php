<?php

/**
 * Contains SpeedStep class
 *
 * @package Devices
 */
 
/**
 * Speed step device
 *
 * Provides speed step handling.
 *
 * @package Devices
 */

class SpeedStep extends Device {

  /**
   * Static device type
   */
  private static $stype = 'speed_step';
  /**
   * Device type
   */
  protected $type = 'speed_step';
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
  protected $type_plural = 'speed_steps';
  /**
   * Default device order
   */
  private static $default_order;
  /**
   * Device numbers
   */
  private static $numbers = array();
  /**
   * Device number
   */
  public $number;

  /**
   * Detects the right order
   * @param array $data_frame
   */
  public static function detect_order($data_frame) {
    if (isset($data_frame['speed_steps'][0]))
      self::$default_order = array(array(1));
    if (isset($data_frame['heat_meters'][1]))
      self::$default_order = array(array(1, 2));
    if (isset($data_frame['speed_steps'][0]))
      self::$default_order = array(array(1, 2, 3));
    if (isset($data_frame['heat_meters'][1]))
      self::$default_order = array(array(1, 2, 3, 4));
    if ($data_frame === array())
      self::$default_order = array(array());
    foreach (self::$default_order[0] as $item) {
      self::$numbers[] = $data_frame['speed_steps'][$item - 1]['output'] + 1;
    }
  }

  /**
   * Detects the right number
   */
  public function init() {
    $this->number = self::$numbers[$this->no - 1];
  }
  
  /**
   * Checks the device number's correctness
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
    return parent::get_by('SpeedStep', $order);
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
    if (!isset($data_frame['speed_steps']))
      return null;
    return $data_frame['speed_steps'][$this->no - 1]['value'];
  }

  /**
   * Renders device as a preview box (mini graph)
   */
  public function render_box() {
    $alias = $this->get_alias();
    $data = DataFrame::open();
    $value = Loc::l($this->fetch_by($data));
    echo <<<code
     <div class="box" id="speed_step$this->no">
      <a href="?p=speed_steps&no=$this->no">
        <div class="inner">
          <div class="number">$this->number</div> $alias
          <div class="value"><span>$value</span></div>
        </div>
        <img src="?p=speed_steps&image&no=$this->no&size=$_GET[size]" alt="Speed step $this->no Graph" />
      </a>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
      $("#speed_steps #speed_step$this->no").hover(
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
   * Prepare a mini graph
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
    $this->extreme_values();
  }
  
}

?>