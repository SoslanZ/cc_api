<?php
require 'models/class.Model.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Queue extends Model {

  private $queueNum;

  function __construct($__queueNum=null) {
    $this->queueNum = $__queueNum;
  }

  /**
  * Получает список очередей с их весом
  */
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
    if (!$result) {
      $this->exception( mysql_error($db->getConnection()) );
    }
    $resp = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      array_push($resp,array(
        'queue_num'   => $row['extension'],
        'description' => $row['descr'],
        'weight'      => $row['data']
      ));
    }

    return $resp;
  }

  /**
  * Устанавливает вес очереди
  */
  public function setWeight($queueWeight) {
    $this->checkQueueExists();
    if (!$queueWeight) {
      $this->exception( $this->_ERR_PARAMS );
    }
    // вес в БД
    $db = new db(new asteriskDataBase());
    $query = "update queues_details qd
                 set qd.data = ".$queueWeight."
               where qd.keyword = 'weight'
                 and qd.id = ".$this->queueNum;
    $result = mysql_query($query,$db->getConnection());
    if (!$result) {
      $this->exception( mysql_error($db->getConnection()) );
    }
    // вес в конфигах астера
    $exec = 'bin/queue_weight.sh '.$this->queueNum.' '.$queueWeight;
    $err = exec($exec);
    if ($err) {
      $this->exception($err);
    }

    return true;
  }

  public function replaceMembers($queueMembers) {

  }

  public function create() {

  }

  public function delete() {

  }

  public static function reloadModule() {
    $execString = "asterisk -rx 'module reload app_queue.so'";
    $output = exec($execString);
  }

  private function checkQueueExists() {
    if (!$this->queueNum) {
      $this->exception( 'Queue num not set in constructor' );
    }
  }
}
