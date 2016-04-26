<?php
// Include pdo
include('header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

// retrieve the table and key from the path
$uid = $request[0];

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    try {
      $sql = "(SELECT C.Text as Text, C.Score as Score, C.Date as Date,
              R.Name as Ename, R.id as Eid, 'Restaurant' as Etype, 0 as Etypeid
              FROM `Comment` C
              LEFT JOIN `Restaurant` R ON C.Eid=R.id
              WHERE C.Etype=0 AND C.Uid=".$uid.")
              UNION
              (SELECT C.Text as Text, C.Score as Score, C.Date as Date,
              B.Name as Ename, B.id as Eid, 'Bar' as Etype, 1 as Etypeid
              FROM `Comment` C
              LEFT JOIN `Bar` B ON C.Eid=B.id
              WHERE C.Etype=1 AND C.Uid=".$uid.")
              UNION
              (SELECT C.Text as Text, C.Score as Score, C.Date as Date,
              H.Name as Ename, H.id as Eid, 'Hotel' as Etype, 2 as Etypeid
              FROM `Comment` C
              LEFT JOIN `Hotel` H ON C.Eid=H.id
              WHERE C.Etype=1 AND C.Uid=".$uid.")";
      $prep = $pdo->prepare($sql);
      $prep->execute();
    }
    catch (Exception $e) {
      echo json_encode($e->getMessage());
    }
    break;
  case 'PUT':
    //$sql = "update `$table` set $set where id=$key";
    break;
  case 'POST':
    break;
  case 'DELETE':
    // Check privileges
    break;
}

// Execute query
if ($method == 'GET') {
  $results = $prep->fetchAll();
  echo json_encode($results);
}
elseif ($method == 'POST') {
  // Nothing left to show
}


?>
