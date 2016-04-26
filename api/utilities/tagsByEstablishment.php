<?php
// Include pdo
include('header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$etype = $request[0];
$eid = $request[1];

array_keys($input);

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    try {
      $sql = "SELECT COUNT(T.id) as Occurences, T.Name as Name, U.id as Uid, U.Name as UName
              FROM `AddsTag` A
              LEFT JOIN `User` U ON A.Uid = U.id
              LEFT JOIN `Tag` T ON A.Tid = T.id
              WHERE A.Eid=".$eid." AND A.Etype=".$etype."
              GROUP BY T.id";
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
