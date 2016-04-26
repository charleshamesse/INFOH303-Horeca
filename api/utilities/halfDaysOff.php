<?php
// Include pdo
include('header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$rid = $request[0];

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    try {
      $sql = "SELECT HD.Day, HD.Period, CO.Did
              FROM `ClosesOn` CO
              LEFT JOIN `HalfDay` HD ON CO.Did = HD.Did
              WHERE CO.Rid=".$rid."";
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
