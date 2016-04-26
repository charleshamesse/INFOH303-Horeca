<?php
// Include pdo
include('utilities/header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table = 'Comment'; //preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;

array_keys($input);

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    try {
      $sql = "select * from `$table`".($key?" WHERE id=$key":'');
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

    // Input values and date
    $uid = $input['Uid'];
    $eid = $input['Eid'];
    $etype = $input['Etype'];
    $score = $input['Score'];
    $text = addslashes(htmlspecialchars($input['Text']));

    try {
      $sql = "INSERT INTO
      `$table` (Eid, Etype, Uid, Score, Text)
      VALUES('".$eid."', '".$etype."', '".$uid."', '".$score."', '".$text."')";
      $prep = $pdo->prepare($sql);
      $prep->execute();
    }
    catch (Exception $e) {
      echo json_encode($e->getMessage());
    }

    break;
  case 'DELETE':
    // Check privileges
    $auth = new Auth();
    if($auth->getPrivileges() == 'Admin') {
      $result = "Ok";
      try {
        $sql = "DELETE FROM `$table` WHERE id=$key";
        $prep = $pdo->prepare($sql);
        $prep->execute();
        $result = "Success! " . $prep->errorInfo();
      }
      catch (Exception $e) {
        echo $result = $e->getMessage();
      }
    }
    else {
      $result = "Rejected";
    }
    echo json_encode($result);
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
