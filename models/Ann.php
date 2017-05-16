<?php
require 'models/DialPlan.php';
require 'config/db_asterisk.php';
require 'config/db.php';

class Ann extends DialPlan {

  public $annId;

  function __construct($__annId = null) {
    $this->annId = $__annId;
  }

  public function setRecord($__recId, $__recName) {
    if (!$this->annId) {
      throw new Exception('ann_id not set in constructor');
    }

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

  // create new announcement
  public function create($__description, $__recId,$__recName,$__queueNum) {
    $db = new db(new asteriskDataBase());
    $query = "insert into announcement( description,allow_skip,post_dest,return_ivr,noanswer,repeat_msg,recording_id)
                                values('$__description','1','ext-queues,".$__queueNum.",1','0','0','','$__recId')";
    // try insert and get ID
    $result = mysql_query($query,$db->getConnection()) or throw new Exception(mysql_error($db->getConnection()));
    $this->annId = mysql_insert_id($db->getConnection());
    // get err from BIN command
    $err = exec('bin/ann_create.sh '.$this->annId.' '.$__recName.' '.$__queueNum);
    if ($err) {
      // del row because ebana MyIsam
      mysql_query("delete from announcement where announcement_id = ".$this->annId,$db->getConnection());
      throw new Exception($err);
    }
  }

  // drop exists announcement
  public function delete($__annId) {
    $db = new db(new asteriskDataBase());
    $db->closeConnection();
  }
}
