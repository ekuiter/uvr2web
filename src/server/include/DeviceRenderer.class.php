<?php

/**
 * Contains DeviceRenderer class
 *
 * @package Renderers
 */
 
/**
 * Renders devices
 *
 * Abstract parent class for all device types.
 *
 * @package Renderers
 */

abstract class DeviceRenderer {

  /**
   * Current class
   */
  protected $class;
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
   * Creates a new device renderer
   */
  public function __construct() {
    new $this->class(true);
    list($this->type, $this->human_type, $this->human_type_plural, $this->type_plural) = Device::$info;
  }
  
  /**
   * Returns the page title
   */
  public function title() {
    return $this->human_type_plural;
  }

  /**
   * Renders the device
   */
  public function render() {
    try {
      $obj = new $this->class();
      if (!$obj->get_enabled())
        throw new Exception();
      $obj->render_page();
    } catch (Exception $e) {
      $this->overview();
    }
  }
  
  /**
   * Renders the overview page
   */
  private function overview() {
    $timeout = 3000 + $GLOBALS['upload_interval'];
    $overview = Loc::t('overview');
    echo <<<code
    <h3>$overview</h3>
    <div id="$this->type_plural"></div>
    <script type="text/javascript">
    /* <![CDATA[ */
    function live$this->type_plural() {
      $("#loading").css('visibility', 'visible');
      $('#$this->type_plural').load(
        "?p=$this->type_plural&live&size=" + \$("#$this->type_plural").css("width"),
        function() {
          $("#loading").css('visibility', 'hidden');
  				timeouts.push(setTimeout(live$this->type_plural, $timeout));
  			}
  		);
		}
		live$this->type_plural();
    /* ]]> */
</script>
code;
  }

  /**
   * Renders the live section
   */
  public function live() {
    try {
      $obj = new $this->class();
      echo $obj->json();
    } catch (Exception $e) {
      if (!isset($_GET['size']))
        die;
      $objs = eval("return $this->class::get();");
      $array = true;
      $groups = 0;
      foreach ($objs as $obj) {
        if (is_array($obj)) {
          $groups++;
          $array = true;
        } else if ($array) {
            $groups++;
            $array = false;
          }
      }
      $group_per_span = 1;
      $i = 1;
      $array = true;
      $groups = 0;
      echo "<div class=\"span3\">";
      foreach ($objs as $obj) {
        if (is_array($obj)) {
          $groups++;
          $array = true;
          $separator = new Separator($i, $this->type);
          $separator->render();
          $i++;
          foreach ($obj as $obj2) {
            if ($obj2->get_enabled())
              $obj2->render_box();
          }
        } else if ($array) {
            $groups++;
            $array = false;
            if ($obj2->get_enabled())
              $obj2->render_box();
          } else
          if ($obj2->get_enabled())
              $obj2->render_box();
        if ($groups == $group_per_span) {
          echo "</div>\n<div class=\"span3\">";
          $groups = 0;
        }
      }
      echo "</div>";
    }
  }

  /**
   * Renders a mini graph
   */
  public function image() {
    try {
      $obj = new $this->class();
      if (!isset($_GET['size']))
        die;
      $size = (int) str_replace('px', '', $_GET['size']);
      $obj->image($size);
    } catch (Exception $e) {
      die;
    }
  }

  /**
   * Renders the sidebar
   */
  public function sidebar() {
    try {
      $obj = new $this->class();
    } catch (Exception $e) {
    }
    $no = isset($obj) ? $obj->no : null;
    $i = 1;
    $objs = eval("return $this->class::get();");
    foreach ($objs as $obj) {
      if (is_array($obj)) {
        $separator = new Separator($i, $this->type);
        $this->sidebar_add($no, $separator);
        $i++;
        foreach ($obj as $obj2) {
          $this->sidebar_add($no, $obj2);
        }
      } else
        $this->sidebar_add($no, $obj);
    }
  }

  /**
   * Adds a device to the sidebar
   * @param int    $no
   * @param object $obj
   */
  private function sidebar_add($no, $obj) {
    if (!$obj->get_enabled())
      return;
    $alias = $obj->get_alias();
    $objno = property_exists($obj, 'number') ? $obj->number : $obj->no;
    if ($obj instanceof Separator)
      echo "<li class=\"separator\">$alias</li>";
    else
      Renderer::sidebar_link("$alias<span class=\"number\">$objno</span>", "?p=$this->type_plural&amp;no=$obj->no", $no == $obj->no);
  }


}

?>