<?php
// Include pdo
include('utilities/header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table = 'Tag'; //preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
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
  // First of all, check privileges
  $auth = new Auth();
  if($auth->getPrivileges() != 'None') {
    $tag = $input['Tag'];
    $uid = $input['Uid'];
    $etype = $input['Etype'];
    $eid = $input['Eid'];

    try {
      // First, check if the tag already exists:
      $sql = "select * from Tag where Name='".$tag."'";
      $prep = $pdo->prepare($sql);
      $prep->execute();
      $result = $prep->fetch();
      // The tag exists
      if($result) {
        // Check if tag hasn't neen added by this user already
        $sql2 = "select * from AddsTag where Tid='".$result['id']."' and Uid='".$uid."' and Etype='".$etype."' and Eid='".$eid."'";
        $prep2 = $pdo->prepare($sql2);
        $prep2->execute();
        $result2 = $prep2->fetchAll();
        echo json_encode($sql2);
        // Check if it hasn't been added yet
        if(count($result2) != 0) {
          echo json_encode("Error: this tag has already been added by this user on this place");
        }
        else {
          $sql3 = "INSERT INTO AddsTag (Tid, Uid, Etype, Eid)
          VALUES(".$result['id'].", ".$uid.", ".$etype.", ".$eid.")";
          $prep3 = $pdo->prepare($sql3);
          $prep3->execute();
          echo json_encode("Success: entry added in AddsTag");
        }
      }
      // Tag does not exist
      else {
        // Add it to Tag
        $sql2 = "INSERT INTO Tag (Name)
        VALUES('".$tag."')";
        $prep2 = $pdo->prepare($sql2);
        $prep2->execute();
        echo json_encode("Success: entry added in Tag");

        // Get its ID
        $sql3 = "select * from Tag where Name='".$tag."'";
        $prep3 = $pdo->prepare($sql3);
        $prep3->execute();
        $result3 = $prep3->fetch();
        echo json_encode("Success: tag found in Tag");

        // Add it to AddsTag
        $sql4 = "INSERT INTO AddsTag (Tid, Uid, Etype, Eid)
        VALUES(".$result3['id'].", ".$uid.", ".$etype.", ".$eid.")";
        $prep4 = $pdo->prepare($sql4);
        $prep4->execute();
        echo json_encode("Success: entry added in AddsTag");
      }
    }
    catch (Exception $e) {
      echo json_encode($e->getMessage());
    }
  }
  else echo json_encode("Error: you don't have the privileges");
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
      $result = $prep->errorInfo();
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
