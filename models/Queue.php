<?php
#require 'models/class.Model.php';
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
    $this->isQueueSetInConstructor();
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
    $this->isQueueSetInConstructor();
    $db = new db(new asteriskDataBase());
    $phoneList = '';
    $i=0;
    if ( !$db->execute('delete from queues_details where keyword="member" and id='.$this->queueNum) ) {
      $this->exception( mysql_error( $db->getConnection() ) );
    }
    foreach($queueMembers as $key => $value) {
      $phone_num = strlen($value['phone']) == 10?('7'.$value['phone']):$value['phone'];
      $i++;
      $phoneList .= ' '.$phone_num;
      if ( !$db->execute('insert into queues_details(id,keyword,data,flags)
                          values("'.$this->queueNum.'",
                                 "member",
                                 "Local/'.$phone_num.'@from-queue/n,0",
                                 '.($i-1).')') ) {

        $this->exception( mysql_error($db->getConnection()) );
      }
    }

    // run bin
    $exec = 'bin/queue_member.sh '.$this->queueNum.' '.$phoneList;
    $err = exec($exec);
    if ($err) {
      $this->exception($err);
    }
    return true;
  }

  public function create($queueNum,$queueName,$queueMembers) {
    if (!$queueNum || !$queueName) {
      $this->exception( $this->_ERR_PARAMS );
    }
    $db = new db(new asteriskDataBase());
    // вернемся с ошибкой если подобная очередь уже есть
    if ( mysql_num_rows($db->execute('select * from queues_config where extension = '.$queueNum)) > 0 ) {
      $this->exception( 'Queue num already exists' );
    }
    if ( mysql_num_rows($db->execute('select * from queues_config where descr = "'.$queueName.'"')) > 0 ) {
      $this->exception( 'Queue name already exists' );
    }

    if ( !$db->execute('insert into queues_config(extension,descr,ringing,ivr_id,dest,cwignore,agentannounce_id,joinannounce_id,queuewait,use_queue_context)
                                 values("'.$queueNum.'","'.$queueName.'",1,"none","app-blackhole,hangup,1",0,0,0,0,0 )') ) {
      $this->exception( mysql_error($db->getConnection()) );
    }

    foreach($this->getQueueKeyWords() as $key => $value) {
      if ( !$db->execute('insert into queues_details(id,keyword,data,flags)
                                    values("'.$queueNum.'",
                                           "'.$key.'",
                                           "'.$value.'",
                                           0)') ) {
        $err = mysql_error($db->getConnection());
        $db->execute('delete from queues_details where id = '.$queueNum);
        $db->execute('delete from queues_config where extension = '.$queueNum);
        $this->exception( $err );
      }
    }

    $phoneList='';
    $i=0;
    foreach($queueMembers as $key => $value) {
      $phone_num = strlen($value['phone']) == 10?('7'.$value['phone']):$value['phone'];
      $i++;
      $phoneList .= ' '.$phone_num;
      if ( !$db->execute('insert into queues_details(id,keyword,data,flags)
                          values("'.$queueNum.'",
                                 "member",
                                 "Local/'.$phone_num.'@from-queue/n,0",
                                 '.($i-1).')') ) {
        $err = mysql_error($db->getConnection());
        $db->execute('delete from queues_details where id = '.$queueNum);
        $db->execute('delete from queues_config where extension = '.$queueNum);
        $this->exception( $err );
      }
    }

    // run BINs
    $exec = 'bin/queue.sh add '.$queueNum.$phoneList;
    $err = exec($exec);
    if ($err) {
      $db->execute('delete from queues_details where id = '.$queueNum);
      $db->execute('delete from queues_config where extension = '.$queueNum);
      $this->exception($err);
    }

    return true;
  }

  public function delete() {
    $this->isQueueSetInConstructor();
    // del from db
    $db = new db(new asteriskDataBase());
    $db->execute('delete from queues_details where id = '.$this->queueNum);
    $db->execute('delete from queues_config where extension = '.$this->queueNum);
    // run BINS
    $exec = 'bin/queue.sh rm '.$this->queueNum;
    $err = exec($exec);

    return true;
  }

  public static function reloadModuleNow() {
    $execString = "asterisk -rx 'module reload app_queue.so'";
    $output = exec($execString);
  }

  private function isQueueSetInConstructor() {
    if (!$this->queueNum) {
      $this->exception( 'Queue num not set in constructor' );
    }
  }

  private function getQueueKeyWords() {
    return array(
      "announce-frequency" => "0",
      "announce-holdtime" => "no" ,
      "announce-position" => "no",
      "autofill" => "no",
      "eventmemberstatus" => "no",
      "eventwhencalled" => "no",
      "joinempty" => "yes",
      "leavewhenempty" => "no",
      "maxlen" => "0",
      "monitor-format" => "",
      "monitor-join" => "yes",
      "periodic-announce-frequency" => "0",
      "queue-callswaiting" => "silence/1",
      "queue-thankyou" => "",
      "queue-thereare" => "silence/1",
      "queue-youarenext" => "silence/1",
      "reportholdtime" => "no",
      "retry" => "0",
      "ringinuse" => "yes",
      "servicelevel" => "60",
      "strategy" => "ringall",
      "timeout" => "60",
      "weight" => "0",
      "wrapuptime" => "0"
    );
  }
}
