<?php

/**
 * Contains AdminRenderer class
 *
 * @package Renderers
 */

/**
 * Renders admin area
 *
 * Abstract parent class for all Admin pages.
 *
 * @package Renderers
 */

abstract class AdminRenderer {

  /**
   * Admin page class
   */
  protected $class;
  /**
   * Device count
   */
  protected $device_count;
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
   * Success messages
   */
  protected $success = '';
  /*
   * Counts device numbers
   */
  protected $numbers = array();

  /**
   * Creates new admin renderer
   */
  public function __construct() {
    new $this->class(true);
    list($this->type, $this->human_type, $this->human_type_plural, $this->type_plural) = Device::$info;
  }

  /**
   * Returns page title
   */
  public function title() {
    return $this->human_type_plural;
  }

  /**
   * Renders the admin body
   */
  public function render() {
    $change_aliases = Loc::t('change aliases');
    $change_order = Loc::t('change order');
    echo <<<code
    <ul class="nav nav-stacked nav-pills">
      <li><a href="?p=admin&sub=$this->type_plural&aliases">$change_aliases</a></li>
      <li><a href="?p=admin&sub=$this->type_plural&order">$change_order</a></li>
    </ul>
code;
    if (isset($_GET['aliases']))
      $this->aliases();
    else if (isset($_GET['order']))
        $this->order();
  }

  /**
   * Renders the aliases page
   */
  protected function aliases() {
    if (isset($_GET['enable'])) {
      $id = $_GET['enable'];
      $id = (int) $id;
      $id or die;
      $obj = new $this->class($id);
      !$obj->get_enabled() or die;
      $obj->set_enabled(true);
      $this->numbers = array();
      $objs = eval("return $this->class::get();");
      foreach ($objs as $obj) {
        if (is_array($obj))
          foreach ($obj as $obj2)
            $this->numbers[] = $obj2->no;
          else
            $this->numbers[] = $obj->no;
      }
      foreach ($objs as $obj) {
        if (is_array($obj))
          $max = $obj[0]->maximal_number();
        else
          $max = $obj->maximal_number();
        break;
      }
      if ($max > count($this->numbers)) {
        for ($i = 1; $i <= $max; $i++)
          if (!in_array($i, $this->numbers)) {
            $order = eval("return $this->class::get_order();");
            $order[] = $i;
            eval("$this->class::set_order(\$order);");
          }
      }
      $this->add_success(Loc::t('object enabled'));
    }
    if (isset($_POST["{$this->type}1"])) {
      foreach ($_POST as $key => $value) {
        if (substr($key, 0, strlen($this->type)) == $this->type && is_numeric(substr($key, strlen($this->type), 2))) {
          $obj = new $this->class(substr($key, strlen($this->type), 2));
        } else if (substr($key, 0, 9) == 'separator' && is_numeric(substr($key, 9, 2))) {
            $obj = new Separator(substr($key, 9, 2), $this->type);
          } else
          die;
        $alias = $obj->set_alias($value);
      }
      $this->add_success(Loc::t('all aliases'));
    }
    $plural = str_replace('_', ' ', $this->type_plural);
    $change_aliases = Loc::t('change aliases');
    $here_you_can = Loc::t('here you can');
    $specified_order = Loc::t('specified order');
    $here_you_can_2 = Loc::t('here you can 2');
    $save = Loc::t('save');
    $cancel = Loc::t('cancel');
    echo <<<code
    <h3>$change_aliases</h3>
    $this->success
    <p>$here_you_can <a href="?p=admin&sub=$this->type_plural&order">$specified_order</a>$here_you_can_2.</p>
    <form method="post" action="?p=admin&sub=$this->type_plural&aliases" class="form-horizontal">
code;
    $this->numbers = array();
    $i = 1;
    $objs = eval("return $this->class::get();");
    foreach ($objs as $obj) {
      if (is_array($obj)) {
        $this->add(new Separator($i, $this->type));
        $i++;
        foreach ($obj as $obj2)
          $this->add($obj2);
      } else
        $this->add($obj);
    }
    foreach ($objs as $obj) {
      if (is_array($obj))
        $max = $obj[0]->maximal_number();
      else
        $max = $obj->maximal_number();
      break;
    }
    if ($max > count($this->numbers))
      for ($i = 1; $i <= $max; $i++)
      if (!in_array($i, $this->numbers))
        $this->add(new $this->class($i));
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

  /**
   * Renders the order page
   */
  protected function order() {
    if (isset($_GET['remove'])) {
      $id = $_GET['remove'];
      $id = (int) $id;
      $id or die;
      $obj = new $this->class($id);
      $obj->get_enabled() or die;
      $obj->set_enabled(false);
      
      $old_order = eval("return $this->class::get_order();");
      $order = array();
      $i = 0;
      foreach ($old_order as $lvl1) {
        if (is_array($lvl1)) {
        $order[$i] = array();
        foreach ($lvl1 as $lvl2)
          if ($lvl2 != $id)
            $order[$i][] = $lvl2;
        $i++;
      } else
        if ($lvl1 != $id)
            $order[] = $lvl1;
      } 
      eval("$this->class::set_order(\$order);");
      
      $this->add_success(Loc::t('object removed'));
    }
    if (isset($_POST['items'])) {
      $order = array();
      $separators = 0;
      for ($i = 0; $i < count($_POST['items']); $i++) {
        if (substr($_POST['items'][$i], 0, 6) == 'device') {
          $order[] = (int) str_replace('device', '', $_POST['items'][$i]);
        } else if ($_POST['items'][$i] == 'separator') {
            $items = array();
            for ($i++; $i < count($_POST['items']); $i++)
              if (substr($_POST['items'][$i], 0, 6) == 'device')
                $items[] = (int) str_replace('device', '', $_POST['items'][$i]);
              else if ($_POST['items'][$i] == 'separator') {
                  $i--;
                  break;
                }
              $order[] = $items;
          }
      }
      eval("$this->class::set_order(\$order);");
    } else {
      $plural = str_replace('_', ' ', $this->type_plural);
      $change_order = Loc::t('change order');
      $drag_to = Loc::t('drag to');
      $add_separator = Loc::t('add separator');
      $name_separators = Loc::t('name separators');
      $name_separators_2 = Loc::t('name separators 2');
      $here = Loc::t('here');
      echo <<<code
    <h3>$change_order</h3>
    $this->success
    <p>$drag_to</p>
    <p><a href="#" data-no-turbolink id="add_separator">$add_separator</a> ($name_separators <a href="?p=admin&sub=$this->type_plural&aliases">$here</a>$name_separators_2)</p>
    <form id="items" method="POST">
      <ul id="sort">
code;
      $i = 1;
      $objs = eval("return $this->class::get();");
      foreach ($objs as $obj) {
        if (is_array($obj)) {
          $this->add_list(new Separator($i, $this->type));
          $i++;
          foreach ($obj as $obj2)
            $this->add_list($obj2);
        } else
          $this->add_list($obj);
      }
      echo <<<code
      </ul>
    </form>
    <script type="text/javascript">
    /* <![CDATA[ */
      $("#add_separator").on("click", function() {
        $('<li><span class="number">-</span>Separator<div class="delete"><button type="button" class="close" onclick="$(this).parent().parent().fadeOut(function(){\$(this).remove();$(this).trigger(\'sortupdate\')})">x</button></div><input type="hidden" name="items[]" value="separator"/></li>').hide().prependTo('#sort').slideDown(    function() {
          var inputs = $('#items').serialize();
          $.post("?p=admin&sub=$this->type_plural&order", inputs, function(){location.reload()});
      });
      });
      $("#sort").sortable({
          update: function() {
          var inputs = $('#items').serialize();
          $.post("?p=admin&sub=$this->type_plural&order", inputs);
      }
      });
    /* ]]> */
    </script>
code;
    }
  }

  /**
   * Renders an object for editing
   * @param object $obj
   */
  protected function add($obj) {
    $alias = $obj->get_alias(true);
    if ($obj->get_enabled()) {
      if ($obj instanceof Separator) {
        echo <<<code
    <div class="control-group">
      <label class="control-label" for="separator$obj->no">$obj->human_type $obj->no</label>
      <div class="controls">
        <input type="text" name="separator$obj->no" id="separator$obj->no" value="$alias" />
      </div>
    </div>
code;
      } else {
        $this->numbers[] = $obj->no;
        echo <<<code
    <div class="control-group">
      <label class="control-label" for="$this->type$obj->no">$this->human_type $obj->no</label>
      <div class="controls">
        <input type="text" name="$this->type$obj->no" id="$this->type$obj->no" value="$alias" />
      </div>
    </div>
code;
      }
    } else {
      $this->numbers[] = $obj->no;
      $disabled = Loc::t('disabled');
      $enable = Loc::t('enable');
      echo <<<code
    <div class="control-group">
      <label class="control-label" for="$this->type$obj->no">$this->human_type $obj->no</label>
      <div class="controls" style="padding-top:5px">
        $disabled. (<a href="?p=admin&sub=$this->type_plural&aliases&enable=$obj->no">$enable</a>)
      </div>
    </div>
code;
    }
  }

  /**
   * Renders an object for dragging
   * @param object $obj
   */
  protected function add_list($obj) {
    if (!$obj->get_enabled())
      return;
    $alias = $obj->get_alias();
    $number = property_exists($obj, 'number') ? $obj->number : $obj->no;
    $number = property_exists($obj, 'number') && !$obj->number ? '' : $number;
    if ($obj instanceof Separator)
      echo "<li><div class=\"number\">-</div>$alias<div class=\"delete\"><button type=\"button\" class=\"close\" onclick='\$(this).parent().parent().fadeOut(
      function(){
      \$(this).remove();
      var inputs = $(\"#items\").serialize();
      \$.post(\"?p=admin&sub=$this->type_plural&order\", inputs, function(){location.reload()});
      })'>x</button></div><input type=\"hidden\" name=\"items[]\" value=\"separator\"/></li>";
    else
      echo "<li><div class=\"number\">$number</div>$alias<div class=\"delete\"><a class=\"close\" href=\"?p=admin&sub=$this->type_plural&order&remove=$number\">x</a></div><input type=\"hidden\" name=\"items[]\" value=\"device$obj->no\"/></li>";
  }

  /**
   * Adds a success message
   * @param string $message
   */
  protected function add_success($message) {
    $this->success .= <<<code
<div class="alert alert-success">
  $message
  <a href="#" class="close" data-dismiss="alert">&times;</a>
</div>
code;
  }

}

?>