<?php
// db connection
$pdo = new PDO('mysql:host=localhost;dbname=infoh303;charset=utf8', 'horeca', '');

// auth
include('auth.php');

// hash_equals
if(!function_exists('hash_equals')) {
  function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
  }
}
?>
