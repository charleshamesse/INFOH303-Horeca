<?php
// Auth Class
class Auth {
  // Eléments de notre panier
  var $level;

  function getAuthLevel() {
    if(isset($_SESSION['User'])) {
      if($_SESSION['User']['Privileges'] == 1) {
        $level = 2;
      }
      else if($_SESSION['User']['Privileges'] == 0) {
        $level = 1;
      }
    }
    else {
      $level = 0;
    }
    return $level;
  }

  function isAdmin() {
    $level = $this->getAuthLevel();
    return ($level == 2);
  }

  function isUser() {
    $level = $this->getAuthLevel();
    return ($level >= 1);
  }


  function getUserName() {
    $level = $this->getAuthLevel();
    if($level >= 1) {
      return $_SESSION['User']['Name'];
    }
    else {
      return "";
    }
  }

  function getPrivileges() {
    $level = $this->getAuthLevel();

    switch($level) {
      case 0:
      return "None";
      break;
      case 1:
      return "User";
      break;
      case 2:
      return "Admin";
      break;
      default:
      return "None";
      break;
    }

  }
}
?>
