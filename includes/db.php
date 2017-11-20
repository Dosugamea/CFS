<?php
require dirname(__FILE__).'/../config/database.php';
class myPDO extends PDO {
  public function __construct() {
    $this->lastQuery = '';
    $args = func_get_args();
    call_user_func_array('parent::__construct', $args);
  }
  public function exec($sql) {
    $this->lastQuery = $sql;
    $ret = parent::exec($sql);
    if($ret === false) {
      trigger_error('Query failed');
    } else {
      return $ret;
    }
  }
  public function query($sql, $parameters=false) {
    $this->lastQuery = $sql;
    if ($parameters == false) {
      $result = true;
      $ret = parent::query($sql);
    } else {
      $ret = parent::prepare($sql);
      if (!$ret) {
        trigger_error('Prepared query failed');
      }
      $result = $ret->execute($parameters);
    }
    if ($result === false) {
      trigger_error('Prepared query failed\n Parameters: '.json_encode($parameters);
    } elseif($ret === false) {
      trigger_error('Query failed');
    } else {
      return $ret;
    }
  }
  public function prepare($sql, $options = []) {
    $this->lastQuery = $sql;
    return parent::prepare($sql, $options);
  }
  public function errorInfo() {
    $info = parent::errorInfo();
    return $this->lastQuery."\n".$info[2];
  }
};

try{
  $mysql=new myPDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);
}catch(PDOException $e){
  if (strpos($_SERVER['PHP_SELF'], 'main.php') !== false) {
    header("Maintenance: 1");
  } else {
    echo '<h1>无法连接数据库</h1>';
  }
  die();
}

$mysql->query('SET names utf8');
$mysql->query('SET time_zone = "+9:00"');

$unitdb = false;
$livedb = false;

function makedb($to, $from, $throws = true) {
  global $$to, $$from;
  if(!$$to) {
    try{
      $$to=new myPDO('sqlite:'.dirname(__FILE__).'/../'.$$from);
    }catch(PDOException $e){
      if ($throws) {
        trigger_error('打不开'.$$from.'数据库');
      } else {
        return false;
      }
    }  
  }
  return $$to;
}

function getUnitDb () {
  return makedb('unitdb', 'unit_db');
}
function getLiveDb () {
  global $livedb, $marathon_db, $battle_db, $festival_db;
  if (!$livedb) {
    makedb('livedb', 'live_db');
    $livedb->exec('ATTACH "'.dirname(__FILE__).'/../'.$marathon_db.'" as marathon');
    $livedb->exec('ATTACH "'.dirname(__FILE__).'/../'.$battle_db.'" as battle');
    $livedb->exec('ATTACH "'.dirname(__FILE__).'/../'.$festival_db.'" as festival');
  }
  return $livedb;
}

$scenariodb = false;
$subscenariodb = false;

function getScenarioDb () {
  return makedb('scenariodb', 'scenario_db', false);
}

function getSubscenarioDb () {
  return makedb('subscenariodb', 'subscenario_db', false);
}

function getSecretBoxDb () {
  return makedb('secretboxdb', 'secretbox_db', false);
}

function getEventDb () {
  return makedb('eventdb', 'event_db', false);
}

function getChallengeDb () {
  return makedb('challengedb', 'challenge_db', false);
}
?>