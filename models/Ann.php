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
    $this->checkAnnId();

    $db = new db(new asteriskDataBase());
    // update data in DB for freepbx
    $query = "update announcement an
                 set an.recording_id = ".$__recId."
               where an.announcement_id = ".$this->annId;
    $result = mysql_query($query,$db->getConnection());
    if (!$result) {
      $this->exception( mysql_error($db->getConnection()) );
    }

    // update data in extensions file
    if ($__recName) {
      $exec = 'bin/ann_rec_set.sh add '.$this->annId.' '.$__recName;
    } else {
      // if RecName is empty, remove record from hi ann
      $exec = 'bin/ann_rec_set.sh rm '.$this->annId;
    }
    $err = exec($exec);
    if ($err) {
      $this->exception($err);
    }

    return true;
  }

  // create new announcement
  public function create($__description, $__recId,$__recName,$__queueNum) {
    $db = new db(new asteriskDataBase());
    $query = "insert into announcement( description,allow_skip,post_dest,return_ivr,noanswer,repeat_msg,recording_id)
                                values('$__description','1','ext-queues,".$__queueNum.",1','0','0','','$__recId')";
    // try insert and get ID
    if ( !mysql_query($query,$db->getConnection()) ) {
      $this->exception( mysql_error($db->getConnection()) );
    }
    $this->annId = mysql_insert_id($db->getConnection());
    // get err from BIN command
    $err = exec('bin/ann_create.sh '.$this->annId.' '.$__recName.' '.$__queueNum);
    if ($err) {
      // del row because ebana MyIsam
      mysql_query("delete from announcement where announcement_id = ".$this->annId,$db->getConnection());
      $this->exception( $err );
    }

    return true;
  }

  // drop exists announcement
  public function delete() {
    $this->checkAnnId();
    $db = new db(new asteriskDataBase());
    if ( !mysql_query("delete from announcement where announcement_id = ".$this->annId,$db->getConnection()) ) {
      $this->exception( mysql_error($db->getConnection()) );
    }
    $err = exec('bin/ann_delete.sh '.$this->annId);
    if ($err) {
      $this->exception( $err );
    }

    return true;
  }

  private function checkAnnId() {
    if (!$this->annId) {
      $this->exception('ann_id not set in constructor');
    }
  }
}
