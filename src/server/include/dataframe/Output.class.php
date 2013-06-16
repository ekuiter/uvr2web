<?php

/**
 * Contains Output class
 *
 * @package Devices
 */
 
/**
 * Output device
 *
 * Provides output handling.
 *
 * @package Devices
 */

class Output extends Device {

  /**
   * Static device type
  */
  private static $stype = 'output';
  /**
   * Device type 
  */
  protected $type = 'output';
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
  protected $type_plural = 'outputs';
  /**
   * Default device order 
  */
  private static $default_order = array(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13));

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
    return parent::get_by('Output', $order);
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
    return $data_frame['outputs'][$this->no - 1];
  }

  /**
   * Renders device as a preview box (mini graph)
  */
  public function render_box() {
    $alias = $this->get_alias();
    $data = DataFrame::open();
    $state = $this->fetch_by($data) ? 'on' : 'off';
    echo <<<code
    <div class="box" id="output$this->no">
      <a href="?p=outputs&no=$this->no">
        <div class="inner">
          <div class="number">$this->no</div> $alias
          <div class="state_$state"></div>
        </div>
        <img src="?p=outputs&image&no=$this->no&size=$_GET[size]" alt="Output $this->no Graph" />
      </a>
      <script type="text/javascript">
    /* <![CDATA[ */
      $("#outputs #output$this->no").hover(
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
    </div>
code;
  }

  /**
   * Prepares a mini graph
   * @param array $data
   * @param int   $x
   * @param int   $y
  */
  protected function image_data($data, $x, $y) {
    foreach ($data as &$value)
      $value = $value == 1 ? 0 : $y - 1;
    return array_reverse($data);
  }

}

?>