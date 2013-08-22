<?php

// include your config file
$sConfFile = dirname(__FILE__) . '/../data/patrizio.php';
include $sConfFile;

// DB
$oMysqlDb = new mysqli(DB_SERVER, DB_USER, DB_PWD, DB_NAME);
if($oMysqlDb->connect_errno) {
    echo "Failed to connect to MySQL(" . $oMysqlDb->connect_errno . "): " . $oMysqlDb->connect_error;
}

// include all necessary classes and helper methods
require_once("lib/KayakoAPILibrary/kyIncludes.php");
 
// initialize the client
try {
  $config = new kyConfig(KAYAKO_API_SERVER, KAYAKO_API_KEY, KAYAKO_SECRET_KEY);
  $config->setDebugEnabled(true);
  kyConfig::set($config);
}
catch(Exception $e) {
  echo 'impossibile connettersi a Kayako';
}
// Drupal Departmenti ID: 31
// Patrizio user ID: 39
// Isa user ID: 15
// Alberto user ID: 16
// retrive tickets
$aUrlVariables = explode('/', $_SERVER['REQUEST_URI']);
unset($aUrlVariables[0]); // delete the first empty parameter

if(is_numeric($aUrlVariables[1])) {
  $iDepartment = $aUrlVariables[1];
  if(!isset($aUrlVariables[2])) {
    include 'include/ticket-per-user.php';
  }
  else {
    switch($aUrlVariables[2]){
      case 'chart':
        include 'include/ticket-per-status.php';
      break;
      case 'customer':
        include 'include/ticket-per-customer.php';
      break;
      case 'update':
        include 'include/ticket-update.php';
      break;
    }
  }
}
else {
  include 'include/department-list.php';
}
