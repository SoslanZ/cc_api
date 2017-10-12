<?php
require 'config/db_cc_line_tf.php';
require 'config/db.php';

class Callback extends Model {

  private $queueId;
  private $callerId;
  private $status;

  const CALL_FINISH = 0;
  const CALL_EXISTS = 1;

  static private $callBackInstance;

  static public function load($jsonArray) {
    if (array_key_exists('queueId',$jsonArray)) {
      return self::getInstance($jsonArray['queueId'],$jsonArray['callerId']);
    }
    return self::getInstance(null,$jsonArray['callerId']);
  }

  static public function getInstance($queueId, $callerId) {
    if (!self::$callBackInstance) {
      self::$callBackInstance = new Callback($queueId,$callerId);
    } else {
      self::$callBackInstance->setQueueId($queueId);
      self::$callBackInstance->setCallerId($callerId);
    }
    return self::$callBackInstance;
  }

  private function __construct($queueId = null,$callerId = null) {
    $this->setQueueId($queueId);
    $this->setCallerId($callerId);
  }

  public function setQueueId($queueId) {$this->queueId = $queueId;}
  public function setCallerId($callerId) {$this->callerId = $callerId;}
  public function getQueueId() {return $this->queueId;}
  public function getCallerId() {return $this->callerId;}

  /**
  * Call integration script for callback add
  */
  public function add() {
    if (!$this->queueId || !$this->callerId) {
      return "Cannot add callback, some parameters missing";
    } else {
      // Call shell script to add callback to aster
      exec("php ./scripts/add_callback.php ".$this->queueId." ".$this->callerId." 2>&1", $output, $return_var);
      if ($output) {
        return $output;
      }
      // CANT CALL function in this context
      //queue_callback($this->queueId, $this->callerId, 0);
    }
  }

  public function getStatus() {
    if (!$this->callerId) {
      throw new Exception("CallerId not set", 1);
      return;
    }

    $db = new db(new LineTfDatabase());
    $query = "SELECT rc.hangupdate
                FROM rt_calls rc
               WHERE rc.callerid = '$this->callerId'
                 and rc.hangupdate = 0";
                 
    return mysql_num_rows($db->execute($query));
  }

}
