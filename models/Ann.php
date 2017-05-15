<?php
require 'models/DialPlan.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Ann extends DialPlan {

  private $annId;

  function __construct($__annId) {
    if (!$__annId) {
      throw new Exception('ann_id not set in constructor');
    }
    $this->annId = $__annId;
  }

  public function setRecord($__recId, $__recName) {
    $db = new db(new asteriskDataBase());

    // update data in DB for freepbx
    $query = "update announcement an
                 set an.recording_id = ".$__recId."
               where an.announcement_id = ".$this->annId;
    $result = mysql_query($query,$db->getConnection());
    $db->closeConnection();
    if (!$result) {
      throw new Exception('mysql query run error');
    }

    // update data in extensions file
    if ($__recName) {
      $execString = 'bin/rec_set_for_ann.sh add '.$this->annId.' '.$__recName;
    } else {
      // if RecName is empty, remove record from hi ann
      $execString = 'bin/rec_set_for_ann.sh rm '.$this->annId;
    }
    $output = exec($execString);

    return true;
  }

}
