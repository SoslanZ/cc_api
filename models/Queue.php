<?php
require 'models/class.Model.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Queue extends Model {

  private $queueNum;

  function __construct($__queueNum) {
    $this->queueNum = $__queueNum;
  }

  function setWeight($queueWeight) {
    $db = new db(new asteriskDataBase());

    echo "new weight for queue: ".$this->queueNum." - ".$queueWeight;
  }

  function replaceMembers($queueMembers) {
    echo "replaceMembers";
  }

}
