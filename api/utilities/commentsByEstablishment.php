<?php
// Include pdo
include('header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table = 'Comment'; //preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$etype = $request[0];
$eid = $request[1];

array_keys($input);

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    try {
      $sql1 = "SELECT C.Text, C.Date, C.Score, U.Name, U.MailAddress
              FROM `Comment` C
              LEFT JOIN `User` U ON C.Uid = U.id
              WHERE C.Eid=".$eid." AND C.Etype=".$etype."";
      $prep1 = $pdo->prepare($sql1);
      $prep1->execute();

      $sql2 = "SELECT AVG(C.Score) AS AvgScore
                FROM `Comment` C
                LEFT JOIN `User` U ON C.Uid = U.id
                WHERE C.Eid=".$eid." AND C.Etype=".$etype."";
        $prep2 = $pdo->prepare($sql2);
        $prep2->execute();
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
  $results = array(
    "comments" => $prep1->fetchAll(),
    "rating" => $prep2->fetch()
  );
  echo json_encode($results);
}
elseif ($method == 'POST') {
  // Nothing left to show
}


?>
