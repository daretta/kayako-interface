<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Status | Bordello di roba</title>
  </head>
  <body>
    <div id="chart_div" style="width: 100%; height: 100%;"></div>
    <div>
      <h2>Ticket non assegnati a gruppi</h2>
      <?php
      $result = $oMysqlDb->query('SELECT display_id FROM ticket WHERE did = ' . $iDepartment . ' AND oid = 0');
      while ($aRow = $result->fetch_assoc()) {
        echo $aRow['display_id'] . '<br/>';
      }
      ?>
    </div>
  </body>
  <!-- jquery -->
  <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Cliente', 'Ticket'],
        <?php
        $result = $oMysqlDb->query('SELECT oid, count(tid) as tot FROM ticket WHERE did = ' . $iDepartment . ' AND oid != 0 AND sid != 5 GROUP BY oid ORDER BY tot DESC');
        $sVirgola = '';
        while ($aRow = $result->fetch_assoc()) {
          try {
            $oOrganization = kyUserOrganization::get($aRow['oid']);
            $sOrganizationName = $oOrganization->name;
          }
          catch(Exception $e) {
            $sOrganizationName = 'ID ' . $aRow['oid'];
          }
          ?>
          <?php echo $sVirgola; ?>[<?php echo json_encode($oOrganization->name); ?>,<?php echo intval($aRow['tot']); ?>]
          <?php
          $sVirgola = ',';
        }
        ?>
      ]);

      var options = {
        title: 'Ticket per Cliente'
      };

      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    $(document).ready(function () {
      /* reload */
      setTimeout('location.href="http://kayako-interface.patrizio.me/<?php echo $iDepartment; ?>"', 150000);
    });
  </script>
</html>
