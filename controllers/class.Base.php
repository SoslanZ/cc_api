<?php

class BaseController {

  protected $_ERR_RUN_NOT_EXIST = 'Run method not exists';
  protected $_ERR_ACT_RECOGNIZE = 'Action not recognized';
  protected $_ERR_PROCESSING = 'Error in processing routine';

  protected $json;

  function __construct($__json) {
    $this->json = $__json;
  }

  public function run() {
    throw new Exception($this->_ERR_RUN_NOT_EXIST);
  }

  protected function exception($msg) {
    throw new Exception($msg);
  }

}
