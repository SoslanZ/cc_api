<?php
//
// ***  ASTERISK API ROUTER ***
//

/**
 * HOW TO USE?
 * 
 * set controller name in get param ("c" variable) and pass json to POST
 * 
 */
$_ERR_CNTR_NOT_SET   = 'Controller not set in GET params';
$_ERR_CNTR_NOT_EXIST = 'Controller not exist';

set_error_handler(
  function($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
  }
);

function response_error($e) {
  $res = array(
    'ok'  => false,
    'msg' => is_string($e) ? $e : $e->getFile().': '.$e->getMessage().' at '.$e->getLine()
  );
  return json_encode($res);
}

// big try
try {

  if (!isset($_GET['c'])) {
      throw new Exception($_ERR_CNTR_NOT_SET);
  }

  $controller = $_GET['c'];

  if (!file_exists('controllers/class.'.$controller.'.php')) {
    throw new Exception($_ERR_CNTR_NOT_EXIST);
  }

  // pass processing to controller
  $r = require('controllers/class.'.$controller.'.php');
  $className = $controller.'Controller';
  $class = new $className( json_decode(file_get_contents('php://input'),true) );
  $class->run();

} catch (Exception $e) {
  echo response_error($e);
}
