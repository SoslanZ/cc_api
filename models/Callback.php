<?php

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
      throw new Exception("Cannot add callback, some parameters missing", 1);
      return;
    } else {
      $out = exec("php ./scripts/add_callback.php ".$this->queueId." ".$this->callerId);
      if ($out) {
        return $out;
      }
      // CANT CALL this in this context
      //queue_callback($this->queueId, $this->callerId, 0);
    }
  }

  public function getStatus() {
    return self::CALL_FINISH;
    // SQL to asterisk
  }

}
