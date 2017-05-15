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
    $dbLink = new db(new asteriskDataBase());
    $dbLink->closeConnection();
    return 'some list';
  }

  function setWeight($queueWeight) {
    $dbLink = new db(new asteriskDataBase());
    $dbLink->closeConnection();
  }

  function replaceMembers($queueMembers) {
  }

}
