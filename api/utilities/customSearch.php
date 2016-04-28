<?php
// Include pdo
include('header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
array_keys($input);
$globalrequest = false;
if($input["type"] == "all") {
  $globalrequest = true;
}
else {
  // Switch table
  switch($input["type"]) {
    case 0:
    $table = 'Restaurant';
    break;
    case 1:
    $table = 'Hotel';
    break;
    case 2:
    $table = 'Bar';
    break;
    default:
    $globalrequest = true;
  }

}

// Name, location and tags
$sql_tail_1 = "";
$sql_tail_2 = "";
$sql_tail_3a = "";
$sql_tail_3b = "";
if($input["name"] != "") {
  $sql_tail_1 = " WHERE E.Name LIKE '%".$input["name"]."%'";
}
if($input["location"] != "") {
  $sql_tail_2 = ($input["name"] == "") ? " WHERE" : " AND";
  $sql_tail_2 .= " E.Address_City LIKE '%".$input["location"]."%'";
}
if($input["tags"] != "") {
  $sql_tail_3a = " RIGHT JOIN AddsTag A ON E.id = A.Eid
    RIGHT JOIN Tag T ON T.id = A.Tid AND T.Name LIKE '%".$input["tags"]."%'";
  $sql_tail_3b = "GROUP BY E.id HAVING E.id IS NOT NULL";
}


function makeSqlByTable($t, $st1, $st2, $st3a, $st3b) {
  $sql = "SELECT
  E.id as id,
  E.Name as Name,
  E.Address_Street as Address_Street,
  E.Address_Num as Address_Num,
  E.Address_Zip as Address_Zip,
  E.Address_City as Address_City,
  E.Address_Longitude as Address_Longitude,
  E.Address_Latitude as Address_Latitude,
  E.Site as Site,
  E.Tel as Tel,
  E.CreationDate as CreationDate
  FROM `$t` E".$st3a.$st1.$st2.$st3b;
  return $sql;
}




// Type
if($globalrequest == false) {
  $sql = makeSqlByTable($table, $sql_tail_1, $sql_tail_2, $sql_tail_3a, $sql_tail_3b);
}
else {
  $sql =  "(" . makeSqlByTable('Restaurant', $sql_tail_1, $sql_tail_2, $sql_tail_3a, $sql_tail_3b) . ") UNION " .
  "(" . makeSqlByTable('Bar', $sql_tail_1, $sql_tail_2, $sql_tail_3a, $sql_tail_3b) . ") UNION " .
  "(" . makeSqlByTable('Hotel', $sql_tail_1, $sql_tail_2, $sql_tail_3a, $sql_tail_3b) . ")";
}
//echo $sql;

try {
  $prep = $pdo->prepare($sql);
  $prep->execute();
  $results = $prep->fetchAll();
  echo json_encode($results);
}
catch (Exception $e) {
  echo $e;
}

?>
