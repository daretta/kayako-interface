<?php
try {
  $oDepartment = kyDepartment::get($iDepartment);
  $aStatus = array(8, 4, 7);
  $aParsedTicket = array();
  foreach($aStatus as $iStatusId){
    $oStatus = kyTicketStatus::get($iStatusId);
    $oTickets = kyTicket::getAll($oDepartment, $oStatus);
    foreach ($oTickets as $oTicket) {
      //var_dump($oTicket::getAPIFields());
      // tid = ticket id
      // sid = status id
      // uid = user id
      // oid = organization id
      // dev_id = developer id
      $sSql = '
      INSERT INTO ticket (tid, did, display_id, sid, uid, oid, dev_id, subject, creation_date, update_date) VALUES(
      "' . $oTicket->id . '",
      "' . $iDepartment . '",
      "' . $oTicket->displayid . '",
      "' . $oTicket->statusid . '",
      "' . $oTicket->userid . '",
      "' . $oTicket->userorganizationid . '",
      "' . $oTicket->ownerstaffid . '",
      "' . $oMysqlDb->real_escape_string($oTicket->subject) . '",
      "' . $oTicket->creationtime . '",
      "' . $oTicket->lastactivity . '"
      )
      ON DUPLICATE KEY UPDATE
      did = "' . $iDepartment . '",
      sid = "' . $oTicket->statusid . '",
      dev_id = "' . $oTicket->ownerstaffid . '",
      update_date = "' . $oTicket->lastactivity . '"
      ;';
      $oResult = $oMysqlDb->query($sSql);
      $aParsedTicket[] = $oTicket->id;
    }
  }
  // delete closed ticket
  $result = $oMysqlDb->query('SELECT tid FROM ticket WHERE did = ' . $iDepartment . ' AND tid NOT IN(' . implode(',', $aParsedTicket) . ') AND sid != 5;');
  while ($aRow = $result->fetch_assoc()) {
    $oTicket = kyTicket::get($aRow['tid']);
    if($oTicket->statusid == 5 || $oTicket->id != $aRow['tid']) {
      $sSql = 'UPDATE ticket SET sid = "5", dev_id = "' . $oTicket->ownerstaffid . '", update_date = "' . $oTicket->lastactivity . '" WHERE tid = "' . $aRow['tid'] . '"';
      $oResult = $oMysqlDb->query($sSql);
    }
  }
  // statistics
  foreach($aStatus as $iStatusId) {
    $oResult = $oMysqlDb->query('SELECT COUNT(tid) as ticket_count FROM ticket WHERE did = ' . $iDepartment . ' AND sid = ' . $iStatusId);
    $aRow = $oResult->fetch_assoc();
    $sSql = '
    INSERT INTO stats (stats_date, stats_type, stats_value, did) VALUES(
    "' . date("Y-m-d H:i:s") . '",
    "status_' . $iStatusId . '",
    "' . $aRow['ticket_count'] . '",
    "' . $iDepartment . '"
    )
    ON DUPLICATE KEY UPDATE
    stats_value = "' . $aRow['ticket_count'] . '"
    ;';
    $oResult = $oMysqlDb->query($sSql);
  }
  $oResult = $oMysqlDb->query('SELECT COUNT(tid) as ticket_count FROM ticket WHERE did = ' . $iDepartment . ' AND creation_date = "' . date("Y-m-d") . '"');
  $aRow = $oResult->fetch_assoc();
  $sSql = '
  INSERT INTO stats (stats_date, stats_type, stats_value, did) VALUES(
  "' . date("Y-m-d H:i:s") . '",
  "new",
  "' . $aRow['ticket_count'] . '",
  "'. $iDepartment . '"
  )
  ON DUPLICATE KEY UPDATE
  stats_value = "' . $aRow['ticket_count'] . '"
  ;';
  $oResult = $oMysqlDb->query($sSql);
}
catch(Exception $e) {
  echo 'non riesco ad aggiornare i ticket';
}
