<?php
// Include pdo
include('header.php');

// Get user from username
$input = json_decode(file_get_contents('php://input'),true);
try {
  $sql = "SELECT Password FROM User WHERE Name='".$input['Name']."'";
  $prep = $pdo->prepare($sql);

  $prep->execute();
  $result = $prep->fetch();

  $resp = "";
  if(!$result) {
    $resp = "Wrong username";
  }
  else {
    $pw = $result['Password'];
    $recrypt = crypt($input['Password'], $pw);
    try {
      if ( hash_equals($pw, $recrypt) ) {
        $resp = "Success";
      }
      else {
        $resp = "Incorrect password";
      }
    }
    catch(Exception $e) {
      $resp .= $e->getMessage();
    }
  }
  echo json_encode($resp);
}
catch (Exception $e) {
  echo $e->getMessage();
}


?>
