<?php
require 'controllers/class.Base.php';
require 'models/class.Model.php';
require 'models/Queue.php';
require 'models/DialPlan.php';

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
        $this->createQueue( $json['data'] );
        break;
      case 'delete':
        $this->deleteQueue( $json['req'] );
        break;
      case 'replace_members':
        $this->replaceMembers( $json['req']['queue_num'], $json['data']['phones'] );
        break;
      case 'getFreeMemberCount':
        $this->getFreeMemberCount($json['req']['queue_num']);
        break;
      default:
        $this->exception( $this->_ERR_ACT_RECOGNIZE );
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
      Queue::reloadModuleNow();
      echo json_encode(array(
        'ok' => true
      ));
    }
  }

  private function createQueue($data) {
    //$this->exception('not released');
    $q = new Queue();
    if ( $q->create($data['queue_num'],$data['queue_name'],$data['phones']) ) {
      Queue::reloadModuleNow();
      DialPlan::reloadDialPlanNow();
      echo json_encode(array(
        'ok' => true
      ));
    }
  }

  private function deleteQueue($req) {
    //$this->exception('not released');
    $q = new Queue($req['queue_num']);
    if ( $q->delete() ) {
      Queue::reloadModuleNow();
      DialPlan::reloadDialPlanNow();
      echo json_encode(array(
        'ok' => true
      ));
    }
  }

  private function replaceMembers($queueNum,$queueMembers) {
    $q = new Queue($queueNum);
    if ( $q->replaceMembers($queueMembers) ) {
      Queue::reloadModuleNow();
      echo json_encode(array(
        'ok' => true
      ));
    }
  }

  protected function getFreeMemberCount($queueNum) {
    $q = new Queue($queueNum);
    parent::ok( $q->getFreeMemberCount() );
  }

}
