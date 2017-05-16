<?php

class BaseController {
  private $_ERR_RUN_NOT_EXIST = 'Run method not exists';
  private $_ERR_ACT_RECOGNIZE = 'Action not recognized';

  protected $json;

  function __construct($__json) {
    $this->json = $__json;
  }

  public function run() {
    throw new Exception($this->_ERR_RUN_NOT_EXIST);
  }

  protected function err($msg) {
    throw new Exception($msg);
  }

}
