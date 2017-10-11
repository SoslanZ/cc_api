<?php
require 'controllers/class.Base.php';
require 'models/class.Model.php';
require 'models/Callback.php';
require 'models/AutoInformer.php';

class DialerController extends BaseController {

  public function addMultiCb() {
    //parent::err("Method not ready for production");
    //exit();
    // TODO
    foreach ($this->json['callBacks'] as $key => $value) {
      $cb = Callback::load($value);
      $cb->add();
    }
    parent::ok();
  }

  public function getMultiCbStatus() {
    $report = array();
    foreach ($this->json['callBacks'] as $key => $value) {
      $cb = Callback::load($value);
      $el = array();
      $el['callerId'] = $cb->getCallerId();
      $el['status'] = $cb->getStatus();
      array_push($report,$el);
    }
    parent::ok($report);
  }

}
