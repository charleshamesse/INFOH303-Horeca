<?php
// Include pdo
include('utilities/header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table;

if($request[0] != "") {
  $etype = $request[0]+0;
  $eid = $request[1];
  $globalrequest = false;
}
else {
  $globalrequest = true;
}

$sql_tail_r = "E.PriceRange_LowerBound as `PriceRange_LowerBound`,
E.PriceRange_UpperBound as `PriceRange_UpperBound`,
E.Capacity as `Capacity`,
E.TakeAway as `TakeAway`,
E.Delivery as `Delivery`,
null as `Stars`,
null as `ExamplePrice`,
null as `Rooms`,
null as `Smoking`,
null as `Snack`,
0 as `Type`";
$sql_tail_h = "null as `PriceRange_LowerBound`,
null as `PriceRange_UpperBound`,
null as `Capacity`,
null as `TakeAway`,
null as `Delivery`,
E.Stars as `Stars`,
E.ExamplePrice as `ExamplePrice`,
E.Rooms as `Rooms`,
null as `Smoking`,
null as `Snack`,
1 as `Type`";
$sql_tail_b = "null as `PriceRange_LowerBound`,
null as `PriceRange_UpperBound`,
null as `Capacity`,
null as `TakeAway`,
null as `Delivery`,
null as `Stars`,
null as `ExamplePrice`,
null as `Rooms`,
E.Smoking as `Smoking`,
E.Snack as `Snack`,
2 as `Type`";

// Switch table
switch($etype) {
  case 0:
  $table = 'Restaurant';
  $sql_tail = $sql_tail_r;
  break;
  case 1:
  $table = 'Hotel';
  $sql_tail = $sql_tail_h;
  break;
  case 2:
  $table = 'Bar';
  $sql_tail = $sql_tail_b;
  break;
  default:
  $globalrequest = true;
}


// create SQL based on HTTP method
function makeSqlByTable($s_t, $t) {
  return "SELECT E.id as id, E.Name as Name,
  E.Address_Street as Address_Street, E.Address_Num as Address_Num, E.Address_Zip as Address_Zip, E.Address_City as Address_City, E.Address_Longitude as Address_Longitude, E.Address_Latitude as Address_Latitude,
  E.Site as Site,
  E.Tel as Tel,
  E.CreationDate as CreationDate, " . $s_t . ",
  U.Name as Creator,
  U.id as CreatorId
  FROM `$t` E
  LEFT JOIN `User` U ON E.CreatedBy=U.id";
}
switch ($method) {
  case 'GET':
  if($globalrequest == false) {
    $sql = makeSqlByTable($sql_tail, $table) . " " . ($eid?" WHERE E.id=$eid":'');
  }
  else {
    $sql =  "(" . makeSqlByTable($sql_tail_r, 'Restaurant') . ") UNION " .
            "(" . makeSqlByTable($sql_tail_b, 'Bar') . ") UNION " .
            "(" . makeSqlByTable($sql_tail_h, 'Hotel') . ")";
  }
  //echo $sql;
  break;
  case 'PUT':
  //$sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
  //$sql = "insert into `$table` set $set"; break;
  case 'DELETE':
  //$sql = "delete `$table` where id=$key";
  break;
}

// Execute query

try {
  $prep = $pdo->prepare($sql);
  $prep->execute();
  $results = $prep->fetchAll();
  echo json_encode($results);
}
catch (Exception $e) {
  echo $e;
}


// connect to the mysql database
/*
$link = mysqli_connect('localhost', 'user', 'pass', 'dbname');
mysqli_set_charset($link,'utf8');

// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;

// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($link) {
if ($value===null) return null;
return mysqli_real_escape_string($link,(string)$value);
},array_values($input));

// build the SET part of the SQL command
$set = '';
for ($i=0;$i<count($columns);$i++) {
$set.=($i>0?',':'').'`'.$columns[$i].'`=';
$set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
}

// create SQL based on HTTP method
switch ($method) {
case 'GET':
$sql = "select * from `$table`".($key?" WHERE id=$key":''); break;
case 'PUT':
$sql = "update `$table` set $set where id=$key"; break;
case 'POST':
$sql = "insert into `$table` set $set"; break;
case 'DELETE':
$sql = "delete `$table` where id=$key"; break;
}

// excecute SQL statement
$result = mysqli_query($link,$sql);

// die if SQL statement failed
if (!$result) {
http_response_code(404);
die(mysqli_error());
}
// print results, insert id or affected row count
if ($method == 'GET') {
/*
if (!$key) echo '[';
for ($i=0;$i<mysqli_num_rows($result);$i++) {
echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
}
if (!$key) echo ']';

}
elseif ($method == 'POST') {

}

else {
echo mysqli_affected_rows($link);
}


// close mysql connection
// mysqli_close($link);

?>
