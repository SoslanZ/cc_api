<?php
require 'models/class.Model.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Queue extends Model {

  private $queueNum;

  function __construct($__queueNum = null) {
    $this->queueNum = $__queueNum;
  }

  function getQueueList() {
    $db = new db(new asteriskDataBase());

    $query = "SELECT qc.extension,
                     qc.descr,
                     qd.data
                FROM queues_config qc,
                     queues_details qd
               WHERE qd.keyword = 'weight'
                 and qc.extension = qd.id";

    $result = mysql_query($query,$db->getConnection());

    if (!$result) exit('mysql_error');

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<queues>';

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
       echo '<queue queue_num="'.$row['extension'].
                  '" description="'.$row['descr'].
                  '" weight="'.$row['data'].'" />';

    }
    echo '</queues>';
    $db->closeConnection();
    return 'some list';
  }

  function setWeight($queueWeight) {
    $dbLink = new db(new asteriskDataBase());
    $dbLink->closeConnection();
  }

  function replaceMembers($queueMembers) {
  }

}
