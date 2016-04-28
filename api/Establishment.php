<?php
// Include pdo
include('utilities/header.php');

// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
array_keys($input);
// Retrieve the table and key from the path
$table;

if($request[0] != "") {
  $etype = $request[0]+0;
  $eid = $request[1];
  $globalrequest = false;
}
else {
  $globalrequest = true;
}

// Select sql request tails
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

  try {
    $prep = $pdo->prepare($sql);
    $prep->execute();
    $results = $prep->fetchAll();
    echo json_encode($results);
  }
  catch (Exception $e) {
    echo $e;
  }

  break;
  // PUT
  case 'PUT':
  $e = $input["e"];
  switch($e["Type"]) {
    case 0:
    $table = "Restaurant";
    $sql_tail = "PriceRange_LowerBound='".$e["PriceRange_LowerBound"]."',
    PriceRange_UpperBound='".$e["PriceRange_UpperBound"]."',
    Capacity='".$e["Capacity"]."',
    TakeAway='".$e["TakeAway"]."',
    Delivery='".$e["Delivery"]."'";
    break;
    case 1:
    $table = "Hotel";
    $sql_tail = "Stars='".$e["Stars"]."',
    Rooms='".$e["Rooms"]."',
    ExamplePrice='".$e["ExamplePrice"]."'";
    break;
    case 2:
    $table = "Bar";
    $sql_tail = "Smoking='".$e["Smoking"]."',
    Snack='".$e["Snack"]."'";
    break;
  }


  $sql = "UPDATE `$table` SET
  Name='".$e["Name"]."',
  Address_Street='".$e["Address_Street"]."',
  Address_Num='".$e["Address_Num"]."',
  Address_Zip='".$e["Address_Zip"]."',
  Address_City='".$e["Address_City"]."',
  Address_Longitude='".$e["Address_Longitude"]."',
  Address_Latitude='".$e["Address_Latitude"]."',
  Site='".$e["Site"]."',
  Tel='".$e["Tel"]."',
  ".$sql_tail."
  WHERE id=".$e["id"];


  try {
    $count = $pdo->exec($sql);
    echo json_encode($count);
  }
  catch (Exception $e) {
    echo json_encode($e->getMessage());
  }
  break;
  // POST
  case 'POST':
  $e = $input["e"];

  switch($e["Type"]) {
    case 0:
    $table = "Restaurant";
    $sql_tail_k = "PriceRange_LowerBound, PriceRange_UpperBound, Capacity, TakeAway, Delivery";
    $sql_tail_v = " '".$e["PriceRange_LowerBound"]."', '".$e["PriceRange_UpperBound"]."', '".$e["Capacity"]."', '".$e["TakeAway"]."', '".$e["Delivery"]."'";
    break;
    case 1:
    $table = "Hotel";
    $sql_tail_k = "Stars, Rooms, ExamplePrice";
    $sql_tail_v = " '".$e["Stars"]."', '".$e["Rooms"]."', '".$e["ExamplePrice"]."'";
    break;
    case 2:
    $table = "Bar";
    $sql_tail_k = "Smoking, Snack";
    $sql_tail_v = " '".$e["Smoking"]."', '".$e["Snack"]."'";
    break;
  }

  try {
    $sql = "INSERT INTO
    `$table` (Name,
      Address_Street, Address_Num, Address_Zip, Address_City, Address_Longitude, Address_Latitude,
      Site, Tel, CreatedBy,
      ".$sql_tail_k.")
      VALUES('".$e["Name"]."',
      '".$e["Address_Street"]."', '".$e["Address_Num"]."', '".$e["Address_Zip"]."', '".$e["Address_City"]."', '".$e["Address_Longitude"]."', '".$e["Address_Latitude"]."',
      '".$e["Site"]."', '".$e["Tel"]."', '".$e["CreatedBy"]."',
      ".$sql_tail_v.")";
      $prep = $pdo->prepare($sql);
      $prep->execute();
      echo $sql . "hi";
    }
    catch (Exception $ex) {
      echo json_encode($ex->getMessage());
    }

    case 'DELETE':
    $sql = "DELETE FROM `$table` WHERE id=".$eid;
    try {
      $count = $pdo->exec($sql);
      echo json_encode($count);
    }
    catch (Exception $e) {
      echo json_encode($e->getMessage());
    }
    break;
  }

  ?>
