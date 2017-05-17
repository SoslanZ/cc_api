<?php
class Model {

  protected $_ERR_PARAMS  = 'Some params not set';

  protected function exception($msg) {
    throw new Exception($msg);
  }
}
