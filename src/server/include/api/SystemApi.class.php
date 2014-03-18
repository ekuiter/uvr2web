<?php

/**
 * Contains SystemApi class
 *
 * @package Api
 */

/**
 * System API
 *
 * Manipulates system-wide settings.
 *
 * @package Api
 */

class SystemApi {

  function language($language = null) {
    if (in_array($language, Loc::languages())) {
      ApiHelper::authenticate('admin');
      Loc::set_language($language);
    }
    return Loc::get_language();
  }
  
  function backup() {
    ApiHelper::authenticate('admin');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="uvr2web-'.date('Y-m-d').'.sql"');
    require_once dirname(__FILE__).'/../pages/admin/Backup.class.php';
    $backup = new Backup();
    $backup->do_backup();
  }
  
  function uninstall($confirm) {
    if ($confirm != $GLOBALS['cfg']['password'])
      throw new Exception('confirm with database password');
    Config::delete_config();
    DataFrame::delete();
    $this->delete_tables();
    session_destroy();
    ApiHelper::authenticate('admin');
    return 'TODO: implement uninstaller';
  }
  
  private function delete_tables() {
    DB::query('DROP TABLE uvr2web_config');
    DB::query('DROP TABLE uvr2web_data');
    DB::query('DROP TABLE uvr2web_users');
  }

}

?>