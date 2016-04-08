<?php
// Include pdo
include('utilities/header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table = 'User'; //preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
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
    $date = date("Y-m-d");
    $name = $input['Name'];
    $pw = $input['Password'];
    $mail = $input['MailAddress'];

    // Password encryption
    $cost = 10;
    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2a$%02d$", $cost) . $salt;
    $hash = crypt($pw, $salt);

    try {
      $sql = "INSERT INTO
      `$table` (Name, MailAddress, Password, Privileges)
      VALUES('".$name."', '".$mail."', '".$hash."', '0')";
      $prep = $pdo->prepare($sql);
      $prep->execute();
    }
    catch (Exception $e) {
      echo json_encode($e->getMessage());
    }

    break;
  case 'DELETE':
    //$sql = "delete `$table` where id=$key";
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
