<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Status | Bordello di roba</title>
  </head>
  <body>
    <div id="chart_div" style="width: 100%; height: 100%;"></div>
  </body>
  <!-- jquery -->
  <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('date', 'Giorno');
      data.addColumn('number', 'Aperti');
      data.addColumn('number', 'Risposto');
      data.addColumn('number', 'In carico');
      data.addColumn('number', 'Nuovi');
      data.addRows([
        <?php
        $result = $oMysqlDb->query('SELECT DATE_FORMAT(stats_date,"%Y") as stats_year, DATE_FORMAT(stats_date,"%m") as stats_month, DATE_FORMAT(stats_date,"%e") as stats_day, GROUP_CONCAT(stats_value) as stats_values FROM stats WHERE did = ' . $iDepartment . ' GROUP BY stats_date ORDER BY stats_date DESC, stats_type DESC');
        $sVirgola = '';
        while ($aRow = $result->fetch_assoc()) {
          ?>
          <?php echo $sVirgola; ?>[new Date(<?php echo $aRow['stats_year']; ?>,<?php echo (intval($aRow['stats_month']) - 1); ?>,<?php echo intval($aRow['stats_day']); ?>),<?php echo $aRow['stats_values']; ?>]
          <?php
          $sVirgola = ',';
        }
        ?>
      ]);
      var options = {
        title: 'Ticket Status',
        hAxis: {title: 'Day'},
        isStacked: true,
        series: {3: {type: "line"}},
        focusTarget: 'category',
        reverseCategories: true
      };

      var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    $(document).ready(function () {
      /* reload */
      setTimeout('location.href="/<?php echo $iDepartment; ?>/customer"', 150000);
    });
  </script>
</html>
