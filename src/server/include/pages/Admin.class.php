<?php

/**
 * Contains Admin class
 *
 * @package Pages
 */
 
/**
 * Renders admin area
 *
 * Renders admin area and determines the specific admin page.
 *
 * @package Pages
 */

class Admin {
  
  /**
   * Role
   *
   * Admin role is required to use the admin section.
   */
  public $role = 'admin';
  /**
   * Sub section
   */
  private $sub = 'Start';
  /**
   * Sub section object
   */
  private $obj;

  /**
   * Creates the admin area
   */
  public function __construct() {
    if (isset($_GET['sub'])) {
      $this->sub = ucfirst(preg_replace('/[^a-zA-Z0-9_]+/', '', $_GET['sub']));
      $this->sub = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->sub)));
      if (!is_file(dirname(__FILE__).'/admin/' . $this->sub . '.class.php'))
        $this->sub = 'Start';
    }
  }

  /**
   * Gets the page title
   */
  public function title() {
    return Loc::t('admin');
  }
  
  /**
   * Renders the admin area
   */
  public function render() {
    if ($this->sub == 'Start')
      $this->start();
    else {
      require_once dirname(__FILE__).'/admin/' . $this->sub . '.class.php';
      $this->obj = new $this->sub();
      echo '<h2>' . $this->obj->title() . '</h2>';
      $this->obj->render();
    }
  }

  /**
   * Renders the sidebar
   */
  public function sidebar() {
    $this->sidebar_sub(Loc::t('language'), 'start');
    $this->sidebar_sub(Loc::t('users'), 'users');
    $this->sidebar_sub(Loc::t('notifications'), 'notifications');
    $this->sidebar_sub(Loc::t('sensors'), 'sensors');
    $this->sidebar_sub(Loc::t('outputs'), 'outputs');
    $this->sidebar_sub(Loc::t('heat meters'), 'heat_meters');
    $this->sidebar_sub(Loc::t('speed steps'), 'speed_steps');
    $this->sidebar_sub(Loc::t('backup'), 'backup');
    $this->sidebar_sub(Loc::t('uninstall'), 'uninstaller');
  }
  
  /**
   * Adds a sidebar link
   * @param string $title
   * @param string $sub
   */
  private function sidebar_sub($title, $sub) {
    Renderer::sidebar_link($title, "?p=admin&amp;sub=$sub", str_replace(' ', '', ucwords(str_replace('_', ' ', $sub))) == $this->sub);
  }

  /**
   * Renders the language section
   */
  private function start() {
    if (isset($_POST['language'])) {
      switch ($_POST['language']) {
        case Loc::t('english'):
          Loc::set_language('en');
          break;
        case Loc::t('german'):
          Loc::set_language('de');
          break;
        case Loc::t('french'):
          Loc::set_language('fr');
          break;
      }
      echo <<<code
<script type="text/javascript">
  /* <![CDATA[ */
  window.location = "?p=admin";
  /* ]]> */
</script>
code;
    }
    $language = Loc::get_language();
    $english_selected = $language == 'en' ? ' selected' : '';
    $german_selected = $language == 'de' ? ' selected' : '';
    $french_selected = $language == 'fr' ? ' selected' : '';
    $admin_body = Loc::t('admin body');
    $save = Loc::t('save');
    $cancel = Loc::t('cancel');
    $language = Loc::t('language');
    $english = Loc::t('english');
    $german = Loc::t('german');
    $french = Loc::t('french');
    echo <<<code
    <h2>$language</h2>
    <p style="margin-bottom:30px">$admin_body</p>
    <form method="post" action="?p=admin" class="form-horizontal">
        <div class="control-group">
          <label class="control-label" for="language">$language</label>
          <div class="controls">
            <select name="language" id="language" size="1">
              <option$english_selected>$english</option>
              <option$german_selected>$german</option>
              <option$french_selected>$french</option>
            </select>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="submit" value="$save" class="btn btn-primary" />
            <a href="?p=admin" class="btn">$cancel</a>
          </div>
        </div>
      </form>
code;
  }
  
  function live() {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="uvr2web-'.date('Y-m-d').'.sql"');
    require_once dirname(__FILE__).'/admin/Backup.class.php';
    $backup = new Backup();
    $backup->do_backup();
  }

}

?>