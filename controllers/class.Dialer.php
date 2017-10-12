<?php
require 'controllers/class.Base.php';
require 'models/class.Model.php';
require 'models/Callback.php';
require 'models/AutoInformer.php';

class DialerController extends BaseController {

  public function addMultiCb() {
    // array for response report
    $report = array();
    foreach ($this->json['callBacks'] as $key => $value) {
      $el = array();
      $cb = Callback::load($value);
      $el['externalId'] = $cb->getExternalId();
      $el['callerId'] = $cb->getCallerId();
      $el['queueId'] = $cb->getQueueId();
      // try to add callback
      $err = $cb->add();
        // errors report
        if ($err) {
          $el['err'] = $err;
        } else {
          $el['err'] = "";
        }
      array_push($report,$el);
    }
    parent::ok($report);
  }

  public function getMultiCbStatus() {
    $report = array();
    foreach ($this->json['callBacks'] as $key => $value) {
      $cb = Callback::load($value);
      $el = array();
      $el['externalId'] = $cb->getExternalId();
      $el['callerId'] = $cb->getCallerId();
      $el['status'] = $cb->getStatus();
      array_push($report,$el);
    }
    parent::ok($report);
  }

}
