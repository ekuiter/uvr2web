<?php

/**
 * Contains Installer class
 *
 * @package Installer
 */

/**
 * uvr2web installer
 *
 * Installs uvr2web into a database.
 *
 * @package Installer
 */

require_once 'include/Loc.class.php';
require_once 'include/Config.class.php';
require_once 'include/DB.class.php';
require_once 'include/dataframe/FrameCounter.class.php';

class Installer {

  private $errors = '';

  /*
   * Initiates installation process
   */
  public function __construct() {
    $header = Loc::t('header');
    $footer = Loc::t('footer');
    try {
      $config = Config::get_config();
    } catch (Exception $e) {
      $directory = str_replace(' is not writeable', '', $e->getMessage());
      $code = <<<code
    $header
  	<p>Installation is impossible because the <em>$directory</em> directory is not writeable.</p>
  	<p>Use your FTP client to change the permissions of <em>$directory</em> to <em>0777</em>.</p>
  	$footer
code;
      die($code);
    }
    if ($config['active'] != 'false') {
      $code = <<<code
    $header
  	<p>Installation is disabled.</p>
  	<p><a href="index.php">Back to uvr2web</a>.</p>
  	$footer
code;
      die($code);
    }

    $header = Loc::t('header');
    $footer = Loc::t('footer');
    echo <<<code
    $header
    <div style="text-align:left">
    <h2 style="margin:0;border-bottom:1px solid #aaa">uvr2web installation</h2>
code;
    if ($_POST)
      $this->validate();
    else
      $this->form();
    echo <<<code
    </div>
    $footer
code;
  }

  /*
   * Displays install formular
   */
  private function form($server = 'localhost', $username = '', $database = '', $upload_password = '', $upload_interval = '7000', $data_record_interval = '90') {
    $upload_password = $upload_password ? $upload_password : $this->generate_password(24);
    echo <<<code
    <p style="margin:20px 0;font-size:14px">To install uvr2web, you need to fill out these fields.</p>
    <p style="margin:20px 0;font-size:16px;color:red">$this->errors</p>
    <form action="install.php" method="post" enctype="multipart/form-data" style="margin:0">
    <p style="border-bottom:1px solid #aaa;font-weight:bold;margin:25px 0 15px 0">MySQL connection</p>
    <table cellpadding="5" style="font-size:16px">
    <tbody>
    <tr>
      <td style="width:140px"><label for="server">Database server</label></td>
      <td><input type="text" size="40" name="server" id="server" value="$server" /></td>
      <td style="font-size:14px;vertical-align:top"><p style="margin:5px 0 0 0">Only MySQL databases are supported.</p></td>
    </tr>
    <tr>
      <td><label for="username">User name</label></td>
      <td><input type="text" size="40" name="username" id="username" value="$username" /></td>
      <td style="color:#777;font-size:14px;vertical-align:top"><p style="margin:5px 0 0 0">Port: 3306, Prefix: uvr2web_</p></td>
    </tr>
    <tr>
      <td><label for="password">Password</label></td>
      <td><input type="password" size="40" name="password" id="password" /></td>
      <td style="color:#777;font-size:14px"></td>
    </tr>
    <tr>
      <td><label for="database">Database name</label></td>
      <td><input type="text" size="40" name="database" id="database" value="$database" /></td>
      <td style="color:#777;font-size:14px"></td>
    </tr>
    </tbody>
    </table>
    <p style="border-bottom:1px solid #aaa;font-weight:bold;margin:25px 0 15px 0">Arduino connection &nbsp;&nbsp;&nbsp;<span style="font-weight:normal;font-size:16px">If you have questions concerning this section, <a href="mailto:info@elias-kuiter.de">contact me</a>.</span></p>
    <table cellpadding="5" style="font-size:16px">
    <tbody>
    <tr>
      <td style="width:140px"><label for="upload_password">Upload password</label></td>
      <td><input type="text" size="40" name="upload_password" id="upload_password" value="$upload_password" style="margin:0" /><br />
      <span style="color:#777;font-size:12px">An example for a secure password</span></td>
      <td style="font-size:14px;vertical-align:top"><p style="margin:5px 0 0 0">Password used to upload data frames. Use the same value as in the Arduino sketch!</p></td>
    </tr>
    <tr>
      <td><label for="upload_interval">Upload interval</label></td>
      <td><input type="text" size="40" name="upload_interval" id="upload_interval" value="$upload_interval" style="margin:0" /><br />
      <span style="color:#777;font-size:12px">Time in ms + 3000 ms = 10 seconds</span></td>
      <td style="font-size:14px;vertical-align:top"><p style="margin:5px 0 0 0">Time between uploading two data frames. Use the same value as in the Arduino sketch!</p></td>
    </tr>
    <tr>
      <td><label for="data_record_interval">Data record interval</label></td>
      <td><input type="text" size="40" name="data_record_interval" id="data_record_interval" value="$data_record_interval" style="margin:0" /><br />
      <span style="color:#777;font-size:12px">90 data frames * 10 secs = 15 minutes</span></td>
      <td style="font-size:14px;vertical-align:top"><p style="margin:5px 0 0 0">After ... data frames a data record is created. By default this is every ~15 minutes. Decrease for better statistics, increase for better performance.</p></td>
    </tr>
    </tbody>
    </table>
    <p style="border-bottom:1px solid #aaa;font-weight:bold;margin:25px 0 15px 0">Restore backup &nbsp;&nbsp;&nbsp;<span style="font-weight:normal;font-size:16px">(optional)</span></p>
    <table cellpadding="5" style="font-size:16px">
    <tbody>
    <tr>
      <td style="width:140px"><label for="backup">Backup file</label></td>
      <td><input type="file" name="backup" id="backup" style="line-height:0" /></td>
      <td style="font-size:14px">All settings, data frames and users of your old uvr2web installation are restored.</td>
    </tr>
    </tbody>
    </table>
    <p style="margin:15px 0"></p>
    <table cellpadding="5" style="font-size:16px">
    <tbody>
    <tr>
      <td style="width:140px"></td>
      <td><input type="submit" value="Install uvr2web" style="width:150px;height:40px" /></td>
    </tr>
    </tbody>
    </table>
    </form>
code;
  }

  private function form_filled_out() {
    $this->form($_POST['server'], $_POST['username'], $_POST['database'], $_POST['upload_password'], $_POST['upload_interval'], $_POST['data_record_interval']);
  }

  private function add_error($message) {
    $this->errors .= "$message<br />\n";
  }

  private function validate() {
    if (isset($_POST['server']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['database'])
      && isset($_POST['upload_password']) && isset($_POST['upload_interval']) && isset($_POST['data_record_interval'])) {
      try {
        DB::start($_POST['server'], $_POST['username'], $_POST['password'], $_POST['database']);
        DB::connect();
        DB::query('SELECT * FROM WTF');
      } catch (Exception $e) {
        if (stristr($e->getMessage(), 'Error connecting to the database') || stristr($e->getMessage(), 'Database name invalid'))
          $this->add_error('Check your database credentials.');
        else
          $this->add_error('Unknown error connecting to the database.');
      }
      if (strlen($_POST['upload_password']) < 16)
        $this->add_error('The upload password is too short (minimum 16 characters).');
      if (!is_numeric($_POST['upload_interval']))
        $this->add_error('The upload interval has to be a number.');
      if (!is_numeric($_POST['data_record_interval']))
        $this->add_error('The data record interval has to be a number.');
    } else
      $this->add_error('Please fill out all fields.');
    if ($this->errors)
      $this->form_filled_out();
    else
      $this->install();
  }

  /**
   * Installs uvr2web
   */
  private function install() {
    $config = array('active' => 'true',
      'server' => $_POST['server'],
      'username' => $_POST['username'],
      'password' => $_POST['password'],
      'database' => $_POST['database'],
      'upload_password' => $_POST['upload_password'],
      'upload_interval' => (int) $_POST['upload_interval'],
      'data_record_interval' => (int) $_POST['data_record_interval']);
    Config::set_config($config);
    $this->create_tables();
    FrameCounter::set((int) $_POST['data_record_interval'] - 1);
    // very insecure because sql is directly executed.
    // TODO: layer between backup sql <-> query sql
    if ($_FILES['backup']['tmp_name']) { 
      DB::multi_query(file_get_contents($_FILES['backup']['tmp_name']));
      $username = '?';
      $username_2 = '(use login data from the backup)';
      $password = '?';
    } else {
      $username = 'admin';
      $username_2 = '';
      $password = $this->generate_password();
      DB::query("INSERT INTO uvr2web_users (username, password, role) VALUES('admin', '".DB::escape(md5($password))."', 'admin')");
    }
    echo <<<code
    <h2 style="color:green;margin:10px 0">Installation complete.</h2>
    <p>You can log in with:</p>
    <p>User: <strong>$username</strong> $username_2</p>
    <p>Pass: <strong>$password</strong></p>
    <p style="margin:20px 0">To finish the installation, you have to delete <em>install.php</em> with your FTP client.</p>
    <p><a href="index.php">I deleted <em>install.php</em></a>.</p>
code;
  }

  /**
   * Creates the database tables
   */
  private function create_tables() {
    DB::query('CREATE TABLE IF NOT EXISTS uvr2web_data
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                 timestamp DATETIME,
                 data_frame TEXT)
                DEFAULT CHARACTER SET utf8');
    DB::query('CREATE TABLE IF NOT EXISTS uvr2web_config
                (config_key VARCHAR(100),
                 config_value VARCHAR(400))
                DEFAULT CHARACTER SET utf8');
    DB::query('ALTER TABLE uvr2web_config ADD UNIQUE (config_key)');
    DB::query('CREATE TABLE IF NOT EXISTS uvr2web_users
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                 username VARCHAR(100) UNIQUE KEY,
                 password VARCHAR(100),
                 role VARCHAR(100))
                DEFAULT CHARACTER SET utf8');
  }

  function generate_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }
    return $result;
  }

}

?>