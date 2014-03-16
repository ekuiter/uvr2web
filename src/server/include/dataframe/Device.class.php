<?php

/**
 * Contains Device class
 *
 * @package Devices
 */

require_once dirname(__FILE__).'/Sensor.class.php';
require_once dirname(__FILE__).'/Output.class.php';
require_once dirname(__FILE__).'/HeatMeter.class.php';
require_once dirname(__FILE__).'/SpeedStep.class.php';
require_once dirname(__FILE__).'/Separator.class.php';

/**
 * Core class for all device types
 *
 * Abstract parent class for all device types. Provides functions for dataframe and database handling, rendering, aliases, order etc. 
 * Applies on the classes Sensor, Output, HeatMeter and SpeedStep.
 *
 * @package Devices
 */

abstract class Device {

  /**
   * Specific number for each device
  */
  public $no;
  /**
   * Device type 
  */
  protected $type;
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
  protected $type_plural;
  /**
   * Temporary device info
  */
  public static $info;
  /**
   * Whether the device is enabled
  */
  protected $enabled;

  /**
   * Renders device as a preview box (mini graph)
  */
  abstract public function render_box();
  /**
   * Checks the device number's correctness
   * @param int $no
  */
  abstract protected function check_no($no);
  /**
   * Prepares a mini graph
   * @param array $data
   * @param int   $x
   * @param int   $y
  */
  abstract protected function image_data($data, $x, $y);
  /**
   * Fetch device data by data frame
   * @param array $data_frame
  */
  abstract public function fetch_by($data_frame);

  /**
   * Create a new device
   * @param mixed $no
  */
  public function __construct($no = null) {
    $this->human_type = Loc::t(str_replace('_', ' ', $this->type));
    $this->human_type_plural = Loc::t(str_replace('_', ' ', $this->type_plural));
    if ($no === true) {
      self::$info = array($this->type, $this->human_type, $this->human_type_plural, $this->type_plural);
      return;
    }
    if ($no === null)
      $no = isset($_GET['no']) ? $_GET['no'] : null;
    if (!$this->check_no($no))
      throw new Exception('invalid device number');
    $this->no = $no;
    method_exists($this, 'init') ? $this->init() : 0;
  }

  /**
   * Get devices by class and order
   *
   * Useful for iterating.
   * @param string $class
   * @param array  $order
  */
  protected static function get_by($class, $order) {
    $objs = array();
    foreach ($order as $item) {
      if (is_array($item)) {
        $objs2 = array();
        foreach ($item as $item2)
          $objs2[] = new $class($item2);
        $objs[] = $objs2;
      } else
        $objs[] = new $class($item);
    }
    return $objs;
  }

  /**
   * Gets the device order
   * @param string $type
   * @param array  $default_order
  */
  protected static function get_order_by($type, $default_order) {
    $result = DB::query("SELECT config_value FROM uvr2web_config WHERE config_key='{$type}_order'");
    if (DB::get_rows() > 0)
      return unserialize($result[0]['config_value']);
    else {
      DB::query("INSERT INTO uvr2web_config (config_key, config_value) VALUES('{$type}_order', '" . DB::escape(serialize($default_order)) . "')");
      return $default_order;
    }
  }

  /**
   * Sets the device order
   * @param string $type
   * @param array  $order
  */
  public static function set_order_by($type, $order) {
    DB::query("UPDATE uvr2web_config SET config_value='" . DB::escape(serialize($order)) . "' WHERE config_key='{$type}_order'");
  }

  /**
   * Gets the device's alias
   * @param bool $real
  */
  public function get_alias($real = false) {
    $result = DB::query("SELECT config_value FROM uvr2web_config WHERE config_key='$this->type{$this->no}_alias'");
    if (DB::get_rows() > 0) {
      if ($result[0]['config_value'] || $real)
        return $result[0]['config_value'];
      else
        return property_exists($this, 'default_alias') ? $this->default_alias : "$this->human_type $this->no";
    } else {
      DB::query("INSERT INTO uvr2web_config (config_key, config_value) VALUES('$this->type{$this->no}_alias', '')");
      return $real ? '' : "Sensor $this->no";
    }
  }

  /**
   * Sets the device's alias
   * @param string $alias
  */
  public function set_alias($alias) {
    DB::query("UPDATE uvr2web_config SET config_value='" . DB::escape($alias) . "' WHERE config_key='$this->type{$this->no}_alias'");
  }
  
  /**
   * Renders a single device
  */
  public function render_page() {
    $month = isset($_GET['month']) ? $_GET['month'] : 1;
    $this->page_title($month);
    $this->fetch_data($month);
    if ($this->data) {
      $this->chart($month);
      method_exists($this, 'page_additional') ? $this->page_additional() : 0;
    } else
      echo Loc::t('no data');
  }
  
  /**
   * Fetches maximum and minimum values
   * @param string $unit
  */
  protected function extreme_values($unit = '') {
    $data = array();
    foreach ($this->data as $row) {
      $data[] = $row[1];
    }
    $smallest_value = Loc::t('smallest value');
    $highest_value = Loc::t('highest value');
    $min = Loc::l(min($data));
    $max = Loc::l(max($data));
    echo <<<code
    <p>$smallest_value: <strong>$min $unit</strong></p>
    <p>$highest_value: <strong>$max $unit</strong></p>
code;
  }
  
  /**
   * Renders the page title 
  */
  protected function page_title($selected_month = 1) {
    $alias = $this->get_alias();
    $separator = $this->get_separator();
    $separator = $separator ? $separator->get_alias() . ':' : '';
    $number = '「' . (property_exists($this, 'number') ? $this->number : $this->no) . '」';
    $number = property_exists($this, 'number') && !$this->number ? '' : $number;
    echo "<h3>
      <span class=\"number\">$number</span>$separator $alias
      <span style=\"float: right\">
        <select onchange=\"
        if (this.selectedIndex == 0)
          window.location.href = '?p=$this->type_plural&no=$this->no&month=all';
        else
          window.location.href = '?p=$this->type_plural&no=$this->no&month=' + this.selectedIndex;
        \">";
    $months = $this->get_months();
    foreach ($months as $index => $month) {
      $selected = $selected_month == $index ? " selected" : "";
      if ($month == Loc::t('all') && $selected_month == 'all') $selected = " selected";
      echo "<option$selected>$month</option>";
    }
    echo "</select>
      </span>
    </h3>";
  }
  
  protected function get_months() {
    $months = array();
    $next = DB::query('SELECT timestamp FROM uvr2web_data 
                     ORDER BY timestamp ASC LIMIT 1')
            [0]['timestamp'];
    while (strtotime($next) < time()) {
      $next = date('Y-m-d H:i:s', strtotime("+1 month", strtotime($next)));
      $timestamp = Loc::mysql_timestamp($next);
      $timestamp['l'] = 'month';
      $months[] = Loc::l($timestamp);
    }
    $months[] = Loc::t('all');
    return array_reverse($months);
  }

  /**
   * Displays a Highchart
   *
   * Uses AJAX to auto-update the chart.
  */
  public function chart($month = 1) {
    $timeout = $GLOBALS['upload_interval'];
    echo "<div id=\"$this->type$this->no\"></div>";
    $chart = new Highchart(Highchart::HIGHSTOCK);
    $chart->chart->renderTo = "$this->type$this->no";
    $chart->chart->type = 'areaspline';
    $expr = <<<code
    function live$this->type$this->no() {
      $("#loading").css('visibility', 'visible');
      $.ajax({
  			url: "?p=$this->type_plural&live&no=$this->no",
  			success: function(point) {
  				var series = $this->type$this->no.series[0];
  				$this->type$this->no.series[0].addPoint(eval(point), true, false);
  		    $("#loading").css('visibility', 'hidden');
  				timeouts.push(setTimeout(live$this->type$this->no, $timeout));
  			},
  			cache: false
  		});
		}
code;
    if ($month == 1)
      $chart->chart->events->load = new HighchartJsExpr($expr);
    $chart->series[0] = array('name' => $this->get_alias(), 'data' => $this->data);
    if ($month == 'all') {
      $select = "0, {type: 'all'}";
      $chart->rangeSelector->buttons = array(
        array('type' => 'all', 'text' => Loc::t('all'))
      );
    } else {
      $select = "1, {type: 'week', count: 1}";
      $chart->rangeSelector->buttons = array(
        array('type' => 'day', 'count' => 1, 'text' => Loc::t('day')),
        array('type' => 'week', 'count' => 1, 'text' => Loc::t('week')),
        array('type' => 'month', 'count' => 1, 'text' => Loc::t('month'))
      );
    }
    $chart->rangeSelector->buttonTheme = array('width' => 80);
    $render = $chart->render();
    echo <<<code
    <script type="text/javascript">
    /* <![CDATA[ */
        Highcharts.setOptions({
          global: {
            useUTC: false
          }
        });
        var $this->type$this->no = $render
        $this->type$this->no.rangeSelector.clickButton($select, true);
    /* ]]> */
</script>
code;
  }

  /**
   * Fetches all device data from the database
  */
  private function fetch_data($month = 1) {
    if ($month == 'all')
      $result = DB::query('SELECT * FROM uvr2web_data');
    else {
      if (!is_numeric($month) || $month < 1) $month = 1;
      $result = DB::query('SELECT * FROM uvr2web_data
        WHERE timestamp <= NOW() - INTERVAL '.($month-1).' MONTH
          AND timestamp >= NOW() - INTERVAL '.($month  ).' MONTH');
    }
    foreach ($result as $row) {
      $data = array();
      $data[0] = strtotime($row['timestamp']) * 1000;
      $data_frame = unserialize($row['data_frame']);
      $data[1] = $this->fetch_by($data_frame);
      $this->data[] = $data;
    }
    if (!$result) $this->data = array();
  }

  /**
   * Returns the current device data as JSON 
  */
  public function json() {
    header("Content-type: text/json");
    $data = DataFrame::open();
    $x = time() * 1000;
    $y = $this->fetch_by($data);
    return json_encode(array($x, $y));
  }

  /**
   * Generates a mini graph
   *
   * GD library needed.
   * @param int $size
  */
  public function image($size) {
    if ($size == 970) {
      $x = 258;
      $y = 30;
    } else if ($size == 780) {
        $x = 208;
        $y = 30;
      } else if ($size == 600) {
        $x = 154;
        $y = 52;
      } else if ($size < 728) {
        $x = $size - 12;
        $y = 30;
      }
    header("Content-Type: image/png");
    $img = imagecreatetruecolor($x, $y);
    imagesavealpha($img, true);
    $trans_color = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $trans_color);
    $color = imagecolorallocate($img, 0, 128, 192);
    $data = array();
    $result = DB::query("SELECT data_frame FROM uvr2web_data ORDER BY timestamp DESC LIMIT $x");
    foreach ($result as $row) {
      $data_frame = unserialize($row['data_frame']);
      $data[] = (float) $this->fetch_by($data_frame);
    }
    $data = $this->image_data($data, $x, $y);
    for ($i = 0; $i < count($data); $i++) {
      if (isset($data[$i + 1])) {
        imageline($img, $i, $data[$i], $i + 1, $data[$i + 1], $color);
        imageline($img, $i, $data[$i] - 1, $i + 1, $data[$i + 1] - 1, $color);
      } else {
        imagesetpixel($img, $i, $data[$i], $color);
        imagesetpixel($img, $i, $data[$i] - 1, $color);
      }
    }
    imagepng($img);
  }
  
  /**
   * Returns a possible parent separator
  */
  protected function get_separator() {
    $i = 1;
    $class = get_class($this);
    $objs = eval("return $class::get();");
    foreach ($objs as $obj) {
      if (is_array($obj)) {
        $separator = new Separator($i, $this->type);
        $i++;
        foreach ($obj as $obj2)
          if ($this->no == $obj2->no)
            return $separator;
      } else
        if ($this->no == $obj->no)
          return false;
    }
    return null;
  }
  
  /*
   * Checks if device is enabled
   */
  public function get_enabled() {
    if (!$this->enabled) {
      $this->enabled = true;
      $result = DB::query('SELECT config_value FROM uvr2web_config WHERE config_key="'.$this->type.$this->no.'_enabled"');
      if ($result == array())
        DB::query('INSERT INTO uvr2web_config VALUES("'.$this->type.$this->no.'_enabled", "1")');
      else
        $this->enabled = $result[0]['config_value'] == '0' ? false : true;
    }
    return $this->enabled;
  }
  
  /**
   * Saves the enabled flag
   */
  function set_enabled($enabled) {
    $this->get_enabled();
    DB::query('UPDATE uvr2web_config SET config_value="' . ($enabled ? 1 : 0) . '" WHERE config_key="'.$this->type.$this->no.'_enabled"');
    $this->enabled = $enabled;
  }
  
  /*
   * Returns the maximal device number
   */
  public function maximal_number() {
    $result = DB::query('SELECT data_frame FROM uvr2web_data LIMIT 0,1');
    $data = unserialize($result[0]['data_frame']);
    return count($data[$this->type_plural]);
  }

}

?>