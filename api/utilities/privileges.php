<?php
include('auth.php');
$auth = new Auth();
$method = $_SERVER['REQUEST_METHOD'];
if($method == "GET") {
  $user = array(
    'id' => $auth->getUserId(),
    'Name' => $auth->getUserName(),
    'Privileges' => $auth->getPrivileges()
  );
  echo json_encode($user);
}
elseif($method == "POST") {
  $input = json_decode(file_get_contents('php://input'),true);
  switch($input['action']) {
    case "logout":
      session_destroy();
      break;
    default:
      break;
  }
}
?>
