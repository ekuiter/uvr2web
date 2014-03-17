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
  
  function __before() {
    ApiHelper::authenticate();
  }
  
  function index($order = null) {
    return 'TODO: return device order / set device order';
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
      if ($start_length == 4) // fetch_data(s1,2014)
        $end = $start->add('+1 year');
      else if ($start_length == 7) // fetch_data(o1,01-2014)
        $end = $start->add('+1 month');
      else if ($start_length == 10) // fetch_data(hm1,01-01-2014)
        $end = $start->add('+1 day');
    } else {
      if (strstr($end, 'y')) // fetch_data(s2,02-2014,1y)
        $end = $start->add_relative('year', $end);
      else if (strstr($end, 'm')) // fetch_data(s2,10-02-2014,1m)
        $end = $start->add_relative('month', $end);
      else if (strstr($end, 'w')) // fetch_data(o2,01-01-2014,1w)
        $end = $start->add_relative('week', $end);
      else if (strstr($end, 'd')) // fetch_data(o2,01-01-2014,1d)
        $end = $start->add_relative('day', $end);
      else
        $end = (new Date($end))->expand(); // fetch_data(ss1,2013,2014)
    }
    
    if ($start->past(new Date($end)))
      throw new Exception('start date past end date');
    
    return $obj->fetch_data_api($start->expand(), $end);
  }
  
  function thumbnail() {
    return 'TODO: return thumbnail';
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