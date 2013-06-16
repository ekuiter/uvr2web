<?php

/**
 * Contains Uploader class
 *
 * @package Uploader
 */
 
/**
 * Data frame uploader
 *
 * Uploads a data frame.
 *
 * @package Uploader
 */

class Uploader {

  /**
   * Uploads a data frame.
   *
   * Uses GET for writing the data frame to the database. Counts data frames.
   */
  public function __construct() {
    $query = $_SERVER['QUERY_STRING'];
    $split = split('&', $query);
    if ($split[0] !== $GLOBALS['pass'])
      die;

    $df = new DataFrame($query);
    $df->save();
    FrameCounter::add();

    if (FrameCounter::get() >= $GLOBALS['db_frame']) {
      $df->save_to_db();
      FrameCounter::reset();
    }
  }

}

?>