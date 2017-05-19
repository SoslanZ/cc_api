<?php
require 'controllers/class.Base.php';
require 'models/class.Model.php';
require 'models/DialPlan.php';
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
        $this->exception($this->_ERR_ACT_RECOGNIZE);
        break;
    }

  }

  /**
  * Устанавливает запист для масива приветствий
  */
  private function setRecordAll($array_of_ann, $rec) {
    $this->checkAnnArrExists($array_of_ann);
    foreach ($array_of_ann as $key => $value) {
      $ann = new Ann($value['ann_id']);
      if ( !$ann->setRecord($rec['rec_id']?$rec['rec_id']:0,$rec['rec_name']) ) {
        $this->exception($this->_ERR_PROCESSING);
      };
    }
    Ann::reloadDialPlanNow();
    echo json_encode(array(
      'ok' => true
    ));
  }

  /**
  * Создание приветствия с указанием очереди и записи
  */
  private function createAnn($data) {
    $annModel = new Ann();
    $annModel->create( $data['description']."_".date("Y_m_d_h_i"),
                       $data['rec_id'],
                       $data['rec_name'],
                       $data['queue_num'] );
    if ( $annModel->annId ) {
      Ann::reloadDialPlanNow();
      echo json_encode(array(
        'ok' => true,
        'data' => array(
          'ann_id' => $annModel->annId
        )
      ));
    }
  }

  /**
  * Удаляет приветствие более не нужное
  */
  private function deleteAnn($data) {
    $annModel = new Ann($data['ann_id']);
    if ( $annModel->delete() ) {
      Ann::reloadDialPlanNow();
      echo json_encode(array(
        'ok' => true
      ));
    }
  }

  private function checkAnnArrExists($array_of_ann) {
    if (!count($array_of_ann)) {
      $this->exception("Array of anns is empty");
    }
  }

}
