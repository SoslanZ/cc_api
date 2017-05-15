<?php

require 'controllers/class.Base.php';
require 'models/Queue.php';

class QueueController extends BaseController {

  public function run() {
    $act  = $this->json['act'];
    $req  = array_key_exists('req',$this->json) ? $this->json['req'] : '';
    $data = array_key_exists('data',$this->json) ? $this->json['data'] : '';

    switch ($act) {
      case 'set_weight':
        $this->setWeight($req['queueNum'],$data['weight']);
        break;
      case 'replace_members':
        $this->replaceMembers($req['queueNum'],$data['weight']);
      break;
      default:
        $this->err('Act not recognized');
      break;
    }
  }

  private function setWeight($queueNum,$queueWeight) {
    $q = new Queue($queueNum);
    $q->setWeight($queueWeight);
  }

  private function replaceMembers($queueNum,$queueMembers) {
    $q = new Queue($queueNum);
    $q->replaceMembers($queueMembers);
  }

}
