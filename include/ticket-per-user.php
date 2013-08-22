<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Owner | Bordello di roba</title>
  <link rel="stylesheet" href="/css/style.css" />
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
</head>
<body>
<?php
$aDeveloper = array();
$aDivListId = array();
$result = $oMysqlDb->query('SELECT DISTINCT(dev_id) FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id != 0 AND sid != 5 ORDER BY dev_id');
while ($row = $result->fetch_assoc()) {
  $aDeveloper[] = $row['dev_id'];
}
$oResult = $oMysqlDb->query('SELECT COUNT(tid) as ticket_count FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id = 0 AND sid != 5');
$aRow = $oResult->fetch_assoc();
?>
<h2>Non assegnati (<?php echo $aRow['ticket_count']; ?>)</h2>
<div id="open-list">
  <ul id="noone" class="ticket-list">
    <?php
    $result = $oMysqlDb->query('SELECT * FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id = 0 AND sid != 5 ORDER BY sid DESC, update_date ASC');
    while ($row = $result->fetch_assoc()) {
      echo '<li class="status-'. $row['sid'] . '">' . $row['display_id'] . '<br/>' . $row['subject'] . '</li>';
    }
    ?>
  </ul>
</div>
<?php

foreach($aDeveloper as $iDeveloperId) {
  $aDivListId[] = '#developer' . $iDeveloperId;
  try {
    $oDeveloper = kyStaff::get($iDeveloperId);
    $sDeveloperName = $oDeveloper->firstname;
  }
  catch(Exaption $e) {
    $sDeveloperName = 'ID ' . $iDeveloperId;
  }
  $oResult = $oMysqlDb->query('SELECT COUNT(tid) as ticket_count FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id = ' . $iDeveloperId . ' AND sid = 4');
  $aRow = $oResult->fetch_assoc();
  $iMine = $aRow['ticket_count'];
  $oResult = $oMysqlDb->query('SELECT COUNT(tid) as ticket_count FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id = ' . $iDeveloperId . ' AND sid NOT IN(4,5)');
  $aRow = $oResult->fetch_assoc();
  $iTheirs = $aRow['ticket_count'];
  ?>
  <div class="developer-list">
    <h2><?php echo $sDeveloperName; ?> (<?php echo $iMine; ?>/<?php echo $iTheirs; ?>)</h2>
    <ul id="developer<?php echo $iDeveloperId; ?>" class="ticket-list">
      <?php
      $result = $oMysqlDb->query('SELECT * FROM ticket WHERE did = ' . $iDepartment . ' AND dev_id = ' . $iDeveloperId . ' AND sid != 5 ORDER BY sid ASC, update_date ASC');
      while ($row = $result->fetch_assoc()) {
        echo '<li class="status-'. $row['sid'] . '">' . $row['display_id'] . '<br/>' . $row['subject'] . '</li>';
      }
      ?>
    </ul>
  </div>
  <?php
}
?>
</body>
<!-- jquery -->
<script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script>
<!-- mousewheel -->
<script src="js/jquery.mousewheel.js" type="text/javascript"></script>
<script>
$(document).ready(function () {
  /* reload */
  setTimeout('location.href="http://kayako-interface.patrizio.me/<?php echo $iDepartment; ?>/chart"', 150000);
  /* ticket list */
  var totalWidths = 0;
  $('#noone li').each(function(){
      totalWidths += $(this).outerWidth(true);
  });
  $('#noone').width(totalWidths);
  $("#open-list").mousewheel(function(event, delta, deltaX, deltaY) {
      var scrollLeft = $(this).scrollLeft();
      var scrollingdiff = totalWidths/10;
      $(this).scrollLeft(scrollLeft+Math.round(delta*scrollingdiff));
      return false;
  });
  $("#noone,<?php echo implode(',', $aDivListId); ?>").sortable({
    connectWith: ".ticket-list"
  }).disableSelection();
});
</script>
</html>
