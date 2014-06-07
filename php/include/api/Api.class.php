<?php

/**
 * Contains Api class
 *
 * @package Api
 */

require_once dirname(__FILE__).'/ApiFunctions.class.php';

/**
 * uvr2web API
 *
 * Controls API calls.
 *
 * @package Api
 */

class Api {
  
  private $space;
  private $func;
  private $args;
  
  function __construct() {
    try {
      ini_set('session.use_only_cookies', 0);
      if (isset($_GET['auth']))
        session_id($_GET['auth']);
      session_start();
      $this->parse(isset($_GET['call']) ? $_GET['call'] : $_SERVER['QUERY_STRING']);
      $result = $this->call();
      $this->render($result);
    } catch (Exception $e) {
      http_response_code(400);
      $this->render(array(
        'space' => $this->space,
        'func' => $this->func,
        'args' => $this->args,
        'error' => $e->getMessage()));
    }
  }
  
  private function parse($string) {
    $func = @explode('(', $string)[0];
    $space = @explode('.', $func)[0];
    $func = @explode('.', $func)[1];
    if ((!$space && !$func) || $string == 'overview')
      $this->render($this->api_functions_overview()) or die();
    if (!$space || !$func)
      throw new Exception('namespace and function required');
    $all_in_one = @explode(')', @explode('(', $string)[1])[0];
    if (substr($all_in_one, 0, 3) == '!!!')
      $args = array(substr($all_in_one, 3));
    else
      $args = @explode(',', $all_in_one);
    $args = array_map('urldecode', $args);
    $args = array_map('trim', $args);
    if (count($args) == 1 && $args[0] === '') $args = array();
    $this->space = $space;
    $this->func = $func;
    $this->args = $args;
  }
  
  private function call() {
    if (!$this->namespace_valid())
      throw new Exception("undefined API namespace '$this->space'");
    if (!$this->function_valid())
      throw new Exception("undefined API function '$this->func' in namespace '$this->space'");
    if (count($this->args) < $this->arguments_required())
      throw new Exception($this->missing_arguments());
    $space_obj = (new ApiFunctions())->{$this->space};
    $this->before_filter($space_obj);
    return call_user_func_array(array($space_obj, $this->func), $this->args);
  }
  
  private function render($result) {
    if (!$this->content_type()) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($result);
    }
  }
  
  private function content_type() {
    foreach (headers_list() as $header)
      if (strstr($header, 'Content-Type'))
        return $header;
    return false;
  }
  
  private function before_filter($space_obj) {
    if (method_exists($space_obj, '__before'))
      $space_obj->__before();
  }
  
  private function namespaces() {
    return array_keys(get_object_vars(new ApiFunctions()));
  }
  
  private function array_without($func, $array) {
    $functions = [];
    foreach ($array as $function)
      if ($function != $func)
        $functions[] = $function;
    return $functions;
  }
  
  private function api_functions() {
    $functions = array();
    foreach ($this->namespaces() as $namespace)
      $functions[$namespace] = $this->array_without('__before', get_class_methods((new ApiFunctions())->$namespace));
    return $functions;
  }
  
  private function api_functions_overview() {
    $functions = array();
    foreach ($this->api_functions() as $namespace => $functions_in_namespace) {
      $space_obj = (new ApiFunctions())->$namespace;
      foreach ($functions_in_namespace as $function_in_namespace)
        $functions[$namespace][$function_in_namespace] = array(
          'desc' => property_exists($space_obj, $function_in_namespace) ? $space_obj->$function_in_namespace : null,
          'examples' => property_exists($space_obj, $function_in_namespace.'_ex') ? $space_obj->{$function_in_namespace.'_ex'} : null,
          'args' => $this->arguments($namespace, $function_in_namespace)
        );
    }
    return $functions;
  }
  
  private function reflect($space, $func) {
    return new ReflectionMethod((new ApiFunctions())->$space, $func);
  }
  
  private function arguments($space, $func) {
      $reflection = $this->reflect($space, $func);
      $arguments = array();
      foreach ($reflection->getParameters() as $param)
          $arguments[] = $param->name;   
      return $arguments;
  }
  
  private function arguments_required() {
    return $this->reflect($this->space, $this->func)->getNumberOfRequiredParameters();
  }
  
  private function missing_arguments() {
    $num = $this->arguments_required() - count($this->args);
    return 'missing '.$num.' argument'.($num == 1 ? '' : 's');
  }
  
  private function namespace_valid() {
    return in_array($this->space, $this->namespaces());
  }
  
  private function function_valid() {
    foreach ($this->api_functions() as $namespace => $functions_in_namespace)
      if ($this->space == $namespace && in_array($this->func, $functions_in_namespace))
        return true;
    return false;
  }

}

?>