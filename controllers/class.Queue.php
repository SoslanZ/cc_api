<?php

require 'controllers/class.Base.php';
require 'models/Queue.php';

class QueueController extends BaseController {

  public function run() {
    $json = $this->json;

    switch ($json['act']) {
      case 'list_queue':
        $this->getQueueList();
        break;
      case 'set_weight':
        $this->setWeight( $json['req']['queue_num'], $json['data']['weight'] );
        break;
      case 'create':
        //$this->;
        break;
      case 'delete':
        //$this->;
        break;
      case 'replace_members':
        $this->replaceMembers( $json['req']['queue_num'], $json['data']['weight'] );
        break;
      default:
        $this->exception('Act not recognized');
      break;
    }
  }

  private function getQueueList() {
    echo json_encode(array(
      'ok'   => true,
      'data' => Queue::getQueueList()
    ));
  }

  private function setWeight($queueNum,$queueWeight) {
    $q = new Queue($queueNum);
    if ( $q->setWeight($queueWeight) ) {
      echo json_encode(array(
        'ok' => true
      ));
    };
  }

  private function createQueue() {
    $q = new Queue($queueNum);
    $q->create();
  }

  private function deleteQueue() {
    $q = new Queue($queueNum);
    $q->delete();
  }

  private function replaceMembers($queueNum,$queueMembers) {
    $q = new Queue($queueNum);
    $q->replaceMembers($queueMembers);
  }

}
