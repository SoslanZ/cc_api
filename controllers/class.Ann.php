<?php

require 'controllers/class.Base.php';
require 'models/Ann.php';

class AnnController extends BaseController {

  public function run() {
    $json = $this->json;

    switch ($json['act']) {
      case 'set_rec' :
        $this->setRecordAll($json['req']['ann_arr'],$json['data']['rec']);
        break;
      case  'create' :
        $this->createAnn($json['data']);
        break;
      case  'delete' :
        $this->deleteAnn($json['req']);
        break;
      default:
        throw new Exception("Act not recognized");
        break;
    }

  }

  private function setRecordAll($array_of_ann, $rec) {
    if (!count($array_of_ann)) {
      throw new Exception("Array of anns is empty");
    }

    foreach ($array_of_ann as $key => $value) {
      $ann = new Ann($value['ann_id']);
      if ( !$ann->setRecord($rec['rec_id']?$rec['rec_id']:0,$rec['rec_name']) ) {
        throw new Exception("Error Processing Request");
      };
    }
    Ann::dialPlanReloadNow();

    echo json_encode(array(
      'ok' => true
    ));
  }

  private function createAnn($dataJson) {
    $ann = new Ann();
    $ann->create($dataJson['description']."_".date("Y_m_d_h_i"),
                 $dataJson['rec_id'],
                 $dataJson['rec_name'],
                 $dataJson['queue_num']);
    if ($ann->annId) {
      Ann::dialPlanReloadNow();
      echo json_encode(array(
        'ok' => true,
        'data' => array(
          'ann_id' => $ann->annId
        )
      ));
    }

  }

  private function deleteAnn($annId) {

  }

}
