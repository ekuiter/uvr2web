<?php

/**
 * Contains Status class
 *
 * @package Pages
 */
 
/**
 * Renders the status page
 *
 * Renders the status page with an md5 hash and further information.
 *
 * @package Pages
 */

class Status {

  /**
   * Role
   *
   * Admin role is required to use the status section.
   */
  public $role = 'admin';

  /**
   * Gets the page title
   */
  public function title() {
    return Loc::t('status');
  }

  /**
   * Renders the status page
   */
  public function render() {
    $timeout = $GLOBALS['upload_interval'];
    echo <<<code
    <div id="live"></div>
    <script type="text/javascript">
    /* <![CDATA[ */
    function live() {
      $("#loading").css('visibility', 'visible');
      $('#live').load(
        "?p=status&live",
        function() {
          $("#loading").css('visibility', 'hidden');
  				timeouts.push(setTimeout(live, $timeout));
  			}
  		);
		}
		live();
    /* ]]> */
</script>
code;
  }
  
  /**
   * Renders the live section
   */
  public function live() {
    $md5 = md5_file(DataFrame::$file);
    $md51 = substr($md5, 0, 8);
    $md52 = substr($md5, 8, 8);
    $md53 = substr($md5, 16, 8);
    $md54 = substr($md5, 24, 8);
    $frame_counter = FrameCounter::get();
    $md5_hash = Loc::t('md5 hash');
    $frames_uploaded = Loc::t('frames uploaded');
    if ($frame_counter == 0) {
      $frames2go = Loc::t('data record');
      $frames_until = '';
      $minutes2go = '';
      $frames_until_2 = '';
    } else {
      $frames2go = $GLOBALS['db_frame'] - $frame_counter;
      $frames_until = Loc::t('frames until');
      $minutes2go = $GLOBALS['upload_interval'] / 1000 * $frames2go / 60;
      $minutes2go = Loc::l(number_format($minutes2go, 1, '.', ''));
      $frames_until_2 = Loc::t('frames until 2');
    }
    $timeout = $GLOBALS['upload_interval'];
    $current_data_frame = Loc::t('current data frame');
    $last_data_record = Loc::t('last data record');
    $last_data_record_2 = Loc::t('last data record 2');
    $last_data_record_3 = Loc::t('last data record 3');
    if (DataFrame::upload_ok()) {
      $color = 'green';
      $status = Loc::t('status ok');
    } else {
      $color = 'red';
      $status = Loc::t('status failed');
    }
    echo <<<code
    <strong style="display:block;font-size:30px;padding-top:30px">
    <span style="color:$color">$status</span>
    </strong>
    <strong style="display:block;font-size:20px;padding-top:30px">
    $md5_hash: <span style="color:green">$md51 $md52 $md53 $md54</span>
    </strong>
    <strong style="display:block;font-size:20px;padding-top:30px">
    <span style="color:green">$frame_counter</span> $frames_uploaded<br />
    <span style="color:green">$frames2go</span> $frames_until <span style="color:green">$minutes2go</span> $frames_until_2
    </strong>
    <strong style="display:block;font-size:20px;margin:30px 0 -20px 0">$current_data_frame</strong>
code;
    $this->dump_data_frame(DataFrame::open());
    $result = DB::query('SELECT * FROM uvr2web_data ORDER BY timestamp DESC LIMIT 0,1');
    foreach ($result as $row) {
      $data_frame = unserialize($row['data_frame']);
      $timestamp = Loc::mysql_timestamp($row['timestamp']);
      $timestamp['l'] = 'date';
      $date = Loc::l($timestamp);
      $timestamp['l'] = 'time';
      $time = Loc::l($timestamp);
      echo <<<code
      <strong style="display:block;font-size:20px;margin:30px 0 -20px 0">$last_data_record $date $last_data_record_2 $time $last_data_record_3</strong> 
code;
      $this->dump_data_frame($data_frame);
    }
  }
  
  private function dump_data_frame($data_frame) {
    $data_frame = serialize($data_frame);
    echo <<<code
    <p style="margin-top:30px;font-size:11px;line-height:0.9em;background-color:#bbb;color:#fff;padding:5px">
    <span style="display:block;float:left;font-size:40px;margin:15px 5px 0 -25px">「</span>$data_frame<span style="display:block;float:right;font-size:40px;margin:-15px -25px 0 5px">」</span>
    </p>
code;
  }

}

?>