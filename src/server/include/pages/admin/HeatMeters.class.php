<?php

/**
 * Contains HeatMeters class
 *
 * @package AdminPages
 */
 
/**
 * Heat meters admin page
 *
 * Allows to and set aliases.
 *
 * @package AdminPages
 */

class HeatMeters extends AdminRenderer {

  /**
   * Class
   */
  protected $class = 'HeatMeter';

  /**
   * Renders admin section
   */
  public function render() {
    $this->aliases();
  }
  
  /**
   * Renders aliases section
   */
  protected function aliases() {
    if (isset($_POST["separator1"])) {
      foreach ($_POST as $key => $value) {
        if (substr($key, 0, 9) == 'separator' && is_numeric(substr($key, 9, 2))) {
          $obj = new Separator(substr($key, 9, 2), $this->type);
        } else
          die;
        $alias = $obj->set_alias($value);
      }
      $this->add_success(Loc::t('all aliases'));
    }
    $change_aliases = Loc::t('change aliases');
    $here_you_can_heat_meters = Loc::t('here you can heat meters');
    $save = Loc::t('save');
    $cancel = Loc::t('cancel');
    echo <<<code
    <h3>$change_aliases</h3>
    $this->success
    <p>$here_you_can_heat_meters</p>
    <form method="post" action="?p=admin&sub=$this->type_plural&aliases" class="form-horizontal">
code;
    $i = 1;
    $objs = eval("return $this->class::get();");
    foreach ($objs as $obj) {
      if (is_array($obj)) {
        $this->add(new Separator($i, $this->type));
        $i++;
      }
    }
    echo <<<code
    <div class="control-group">
      <div class="controls">
        <input type="submit" value="$save" class="btn btn-primary" />
        <a href="?p=admin&sub=$this->type_plural" class="btn">$cancel</a>
      </div>
    </div>
    </form>
code;
  }

}

?>