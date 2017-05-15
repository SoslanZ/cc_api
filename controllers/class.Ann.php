<?php

require 'controllers/class.Base.php';
require 'models/Ann.php';

class AnnController extends BaseController {

  public function run() {
    $json = $this->json;

    switch ($json['act']) {
      case 'set_rec':
        $this->setRecordAll($json['req']['ann_arr'],$json['data']['rec']);
        break;
      case 'create':
        break;
      case 'delete':
        break;
      default:
        break;
    }

  }

  private function setRecordAll($array_of_ann, $rec) {
    foreach ($array_of_ann as $key => $value) {
      $ann = new Ann($value['ann_id']);
      if ( !$ann->setRecord($rec['rec_id'],$rec['rec_name']) ) {
        throw new Exception("Error Processing Request");
      };
    }
    Ann::dialPlanReloadNow();

    echo json_encode(array(
      'ok' => true
    ));
  }

}
