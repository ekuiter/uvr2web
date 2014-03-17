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
  
  function uninstall() {
    ApiHelper::authenticate('admin');
    return 'TODO: implement uninstaller';
  }

}

?>