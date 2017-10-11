<?php

class BaseController {

  protected $_ERR_RUN_NOT_EXIST = 'Run method not exists';
  protected $_ERR_ACT_RECOGNIZE = 'Action not recognized';
  protected $_ERR_PROCESSING = 'Error in processing routine';

  protected $json;

  function __construct($__json) {
    $this->json = $__json;
  }

  /**
  *  MAIN RUN FUNCTION
  *    call method named in POST JSON in "act" field
  */

  public function run() {
    exit();
    $actionMethod = $this->json['act'];
    if (method_exists($this,$actionMethod)) {

      // method call
      $this->$actionMethod();

    } else {
      $this->exception($this->_ERR_ACT_RECOGNIZE);
    }

  }

  protected function exception($msg) {
    throw new Exception($msg);
  }

  protected function ok($data = null, $msg = null) {
    echo json_encode(array(
      'ok' => true,
      'msg' => $msg,
      'data' => $data
    ));
  }

  protected function err($msg = null) {
    echo json_encode(array(
      'ok' => false,
      'msg' => $msg
    ));
  }

}
