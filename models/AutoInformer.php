<?php
//require 'config/db_asterisk.php';
//require 'config/db.php';

class AutoInformer extends Model {

  public $some;

  function __construct($some = null) {
    $this->some = $some;
  }

  public function add() {
    return true;
  }

}
