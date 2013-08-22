<?php
// DB
$oMysqlDb = new mysqli("mysql1025.servage.net", "kayako", "jsdsd7267fdbhf3e", "kayako");
if ($oMysqlDb->connect_errno) {
    echo "Failed to connect to MySQL(" . $oMysqlDb->connect_errno . "): " . $oMysqlDb->connect_error;
}

// include all necessary classes and helper methods
require_once("lib/KayakoAPILibrary/kyIncludes.php");
 
// initialize the client
$config = new kyConfig("https://support.softecspa.it/api/index.php", "650d0416-ac70-2ae4-9da8-9c006c21f9b2", "MmE4ZjZmZmEtOTA0MS03YTM0LWUxYjAtODBjMjQ2ZTM4ZjRjN2FlZTk5MGYtZTIxZS1iZTM0LWZkODgtNjFmZTdlMmFlNWVi");
$config->setDebugEnabled(true);
kyConfig::set($config);
// Drupal Departmenti ID: 31
// Patrizio user ID: 39
// Isa user ID: 15
// Alberto user ID: 16
// retrive tickets
$aUrlVariables = explode('/', $_SERVER['REQUEST_URI']);
unset($aUrlVariables[0]); // delete the first empty parameter


$result = $oMysqlDb->query('SELECT tid FROM ticket WHERE did = 37');
while ($aRow = $result->fetch_assoc()) {
  if($aRow['tid'] == '57374' || $aRow['tid'] == '79406') {
    continue;
  }
  $oTicket = kyTicket::get($aRow['tid']);
  if(is_null($oTicket->userorganizationid)) {
    var_dump($oTicket->displayid);
  }
  else {
    $sSql = 'UPDATE ticket SET oid = "' . $oTicket->userorganizationid . '" WHERE tid = "' . $oTicket->id . '"';
    $oResult = $oMysqlDb->query($sSql);
  }
}
