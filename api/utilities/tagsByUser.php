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
      $sql = "(SELECT T.Name as Name, E.Name as Ename, E.id as Eid, 0 as Etype
              FROM `AddsTag` A
              RIGHT JOIN `Tag` T ON A.Tid=T.id
              RIGHT JOIN `Restaurant` E ON A.Eid = E.id
              WHERE A.Uid=".$uid." AND A.Etype = 0)
              UNION
              (SELECT T.Name as Name, E.Name as Ename, E.id as Eid, 1 as Etype
              FROM `AddsTag` A
              RIGHT JOIN `Tag` T ON A.Tid=T.id
              RIGHT JOIN `Bar` E ON A.Eid = E.id
              WHERE A.Uid=".$uid." AND A.Etype = 1)
              UNION
              (SELECT T.Name as Name, E.Name as Ename, E.id as Eid, 2 as Etype
              FROM `AddsTag` A
              RIGHT JOIN `Tag` T ON A.Tid=T.id
              RIGHT JOIN `Hotel` E ON A.Eid = E.id
              WHERE A.Uid=".$uid." AND A.Etype = 2)";

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
