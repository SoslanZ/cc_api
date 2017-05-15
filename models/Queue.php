<?php
require 'models/class.Model.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Queue extends Model {

  private $queueNum;

  function __construct($__queueNum) {
    if (!$__queueNum) {
      throw new Exception('Queue Num not set');
    }
    $this->queueNum = $__queueNum;
  }

  public static function getQueueList() {
    $db = new db(new asteriskDataBase());

    $query = "SELECT qc.extension,
                     qc.descr,
                     qd.data
                FROM queues_config qc,
                     queues_details qd
               WHERE qd.keyword = 'weight'
                 and qc.extension = qd.id";

    $result = mysql_query($query,$db->getConnection());
    $db->closeConnection();

    if (!$result) {
      throw new Exception('mysql query run error');
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<queues>';

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
       echo '<queue queue_num="'.$row['extension'].
                  '" description="'.$row['descr'].
                  '" weight="'.$row['data'].'" />';

    }
    echo '</queues>';

    return 'some list';
  }

  function setWeight($queueWeight) {
    if (!$queueWeight || )
    $db = new db(new asteriskDataBase());

    $query = "update queues_details qd
                 set qd.data = ".$queueWeight."
               where qd.keyword = 'weight'
                 and qd.id = ".$this->queueNum;
    $result = mysql_query($query,$db->getConnection());
    $db->closeConnection();

    if (!$result) {
      throw new Exception('mysql query run error');
    }

    echo json_encode(array(
      'ok' => true
    ));

  }

  function replaceMembers($queueMembers) {
  }

}
