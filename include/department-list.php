<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Status | Bordello di roba</title>
  </head>
  <body>
    <ul>
    <?php
    $oDepartments = kyDepartment::getAll();
    foreach ($oDepartments as $oDepartment) {
      echo '<li><a href="/' . $oDepartment->id . '">' . $oDepartment->title . '</a></li>';
    }
    ?>
    </ul>
  </body>
<ul>
</ul>
