<?php

/**
 * Contains DeviceApi class
 *
 * @package Api
 */

/**
 * Device API
 *
 * Retrieve device data.
 *
 * @package Api
 */

class DeviceApi {
  
  public $index = 'Returns (login required) or sets (admin-only) the device structure (including aliases and order).';
  public $index_ex = array('device.index', 'device.index(!!!{"sensors":{"...":[...]},"outputs":{"...":[...]},"heat_meters":{"...":[...]},"speed_steps":{"...":[...]}})');
  public $read = 'Reads device data (login required).';
  public $read_ex = array('device.read', 'device.read(s1)', 'device.read(s1,latest)', 'device.read(s1,all)', 'device.read(s1,today)', 'device.read(s1,2014)', 'device.read(o1,01-2014)', 'device.read(hm1,01-01-2014)', 'device.read(s2,02-2014,1y)', 'device.read(ss1,2013,2014)');
  public $thumbnail = 'Renders a PNG thumbnail showing device data (login required).';
  public $thumbnail_ex = array('device.thumbnail(s1)', 'device.thumbnail(s1,970)', 'device.thumbnail(s1,640,480)', 'device.thumbnail(s1,640,480,ff0000)', 'device.thumbnail(s1,,,f00)');
  
  function __before() {
    ApiHelper::authenticate();
  }
  
  function index($structure = null) {
    if ($structure) {
      ApiHelper::authenticate('admin');
      $structure = json_decode($structure, true);
      if (isset($structure['sensors'])) $this->set_structure('Sensor', $structure['sensors']);
      if (isset($structure['outputs'])) $this->set_structure('Output', $structure['outputs']);
      if (isset($structure['heat_meters'])) $this->set_structure('HeatMeter', $structure['heat_meters']);
      if (isset($structure['speed_steps'])) $this->set_structure('SpeedStep', $structure['speed_steps']);
    }
    return array(
      'sensors' => $this->get_structure('Sensor'),
      'outputs' => $this->get_structure('Output'),
      'heat_meters' => $this->get_structure('HeatMeter'),
      'speed_steps' => $this->get_structure('SpeedStep')
    );
  }
  
  private function to_underscore($camel_case) {
    return substr(strtolower(preg_replace('/([A-Z])/', '_$1', $camel_case)), 1);
  }
  
  private function get_structure($class) {
    $i = 1;
    $structure = array();
    foreach ($class::get_order() as $group) {
      $devices = array();
      foreach ($group as $device)
        $devices[] = array('id' => $device, 'alias' => (new $class($device))->get_alias());
      $structure[(new Separator($i, $this->to_underscore($class)))->get_alias()] = $devices;
      $i++;
    }
    return $structure;
  }
  
  private function set_structure($class, $structure) {
    $reporting = error_reporting();
    error_reporting(0);
    $i = 1;
    foreach ($structure as $separator => $group) {
      if (error_get_last()) throw new Exception('structure parse failed');
      (new Separator($i, $this->to_underscore($class)))->set_alias($separator);
      foreach ($group as $device) {
        $obj = new $class($device['id']);
        $alias = $device['alias'];
        if (error_get_last()) throw new Exception('structure parse failed');
        $obj->set_alias($alias);
      }
      $i++;
    }
    $order = array();
    $i = 0;
    foreach ($structure as $group) {
      foreach ($group as $device)
        $order[$i][] = $device['id'];
      $i++;
    }
    if (error_get_last()) throw new Exception('structure parse failed');
    $class::set_order($order);
    error_reporting($reporting);
  }
  
  function read($device = null, $start = null, $end = null) {  
    if (!$device || $device == 'all')
      return DataFrame::open();
    
    $class = $this->get_class($device);
    $number = $this->get_number($device);
    $obj = new $class($number);
    
    if (!$start || $start == 'latest')
      return $obj->fetch_by(DataFrame::open());
    else if ($start == 'all')
      return $obj->fetch_data_api('all');
    else if ($start == 'today')
      $start = strftime('%Y-%m-%d');
    
    $start_length = strlen($start);
    $start = new Date($start);
    
    if ($end === null) {
      if ($start_length == 4) // device.read(s1,2014)
        $end = $start->add('+1 year');
      else if ($start_length == 7) // device.read(o1,01-2014)
        $end = $start->add('+1 month');
      else if ($start_length == 10) // device.read(hm1,01-01-2014)
        $end = $start->add('+1 day');
    } else {
      if (strstr($end, 'y')) // device.read(s2,02-2014,1y)
        $end = $start->add_relative('year', $end);
      else if (strstr($end, 'm')) // device.read(s2,10-02-2014,1m)
        $end = $start->add_relative('month', $end);
      else if (strstr($end, 'w')) // device.read(o2,01-01-2014,1w)
        $end = $start->add_relative('week', $end);
      else if (strstr($end, 'd')) // device.read(o2,01-01-2014,1d)
        $end = $start->add_relative('day', $end);
      else
        $end = (new Date($end))->expand(); // device.read(ss1,2013,2014)
    }
    
    if ($start->past(new Date($end)))
      throw new Exception('start date past end date');
    
    return $obj->fetch_data_api($start->expand(), $end);
  }
  
  function thumbnail($device, $size = null, $y = null, $color = null) {
    $class = $this->get_class($device);
    $number = $this->get_number($device);
    $obj = new $class($number);
    $size = (int) str_replace('px', '', $size);
    $obj->image($size, $y, $color);
  }
  
  private function get_class($device) {
    if (strstr($device, 'ss'))
      return 'SpeedStep';
    else if (strstr($device, 's'))
      return 'Sensor';
    else if (strstr($device, 'o'))
      return 'Output';
    else if (strstr($device, 'hm'))
      return 'HeatMeter';
    else
      throw new Exception('invalid device type');
  }
  
  private function get_number($device) {
    return (int) strtr($device, array('s' => '', 'o' => '', 'hm' => ''));
  }
  
}

class Date {
  
  private $date;
  
  function __construct($string) {
    $this->date = $string;
    $this->expand();
  }
  
  function expand() {
    if (strlen($this->date) == 4)
      $this->date = "$this->date-01-01";
    else if (strlen($this->date) == 7)
      $this->date = "$this->date-01";
    else if (strlen($this->date) == 10);
    else throw new Exception('invalid date format');
    if (!strtotime($this->date) || strtotime($this->date) < 0)
      throw new Exception('invalid date format');
    $this->date = strftime('%Y-%m-%d', strtotime($this->date));
    return $this->date;
  }
  
  function add($add) {
    return strftime('%Y-%m-%d', strtotime($add, strtotime($this->date)));
  }
  
  function add_relative($format, $add) {
    return $this->add('+'.str_replace($format[0], '', $add)." $format");
  }
  
  function past($date) {
    return strtotime($date->expand()) - strtotime($this->expand()) <= 0;
  }
  
}
  
?>