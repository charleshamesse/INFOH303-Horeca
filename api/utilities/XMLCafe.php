<?php
// Include pdo
include('header.php');
?>
<!DOCTYPE html>
<html lang="en" ng-app="horeca" ng-cloak>
<head>
  <meta charset="utf-8">
  <title>Horeca in Brussels</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- CSS -->
  <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" media="screen">
  <link rel="stylesheet" href="../../assets/css/custom.css" media="screen">
</head>
<body>
  <div class="container">
    <h1>.brussels XML Import</h1>
    <?php
    if(!$_GET['xml_path'])
    $path = '../../assets/xml/Cafes.xml';
    else
    $path = $_GET['xml_path'];

    try {
      $xml = simplexml_load_file($path) or die("Error: Cannot create object");
      $i = 0;
      while($xml->Cafe[$i] != null) {
        $cafe = $xml->Cafe[$i];
        $sql = buildSql($cafe, $pdo);
        echo "<h3>" . $cafe->Informations->Name . "</h3>"; //"<pre>" . $sql . "</pre>";
        try {
          $prep = $pdo->prepare($sql);
          if($prep->execute()) {
            $id = $pdo->lastInsertId();
            echo "<span class='text-success'>Establishment successfully inserted. ID: " . $id . "</span><br />";
            insertComments($cafe->Comments, $id, $pdo);
            insertTags($cafe->Tags, $id, $pdo);
          }
          else {
            echo $prep->errorCode();
            echo var_dump($prep->errorInfo());
            echo $sql;
          }
        }
        catch (Exception $e) {
          echo $e->getMessage();
        }
        $i++;
      }
    }
    catch (Exception $e) {
      $result = "Error parsing XML file: " . $e;
    }

    ?>
  </div>
</body>
</html>


<?php
function buildSql($cafe, $pdo) {
  // Basic info
  $site = $cafe->Informations->Site["link"] ? $cafe->Informations->Site["link"] : "";
  $tel = $cafe->Informations->Tel ? $cafe->Informations->Tel : "";
  $smoking = $cafe->Informations->Smoking ? 1 : 0;
  $snack = $cafe->Informations->Snack ? 1 : 0;
  $dateElements = explode("/", $cafe["creationDate"]);
  $date = new DateTime();
  $date->setDate($dateElements[2], $dateElements[1], $dateElements[0]);
  $creationDate = $date->format('Y-m-d G:i:s');

  // User
  $userid = getUserIdByNameOrCreateIt($cafe["nickname"], $pdo, 1);

  $sql = "INSERT INTO Bar
  (Name,
    Address_Street,
    Address_Num,
    Address_Zip,
    Address_City,
    Address_Longitude,
    Address_Latitude,
    Site,
    Tel,
    CreationDate,
    CreatedBy,
    Smoking,
    Snack)
    VALUES
    ('".addslashes($cafe->Informations->Name)."',
    '".addslashes($cafe->Informations->Address->Street)."',
    '".$cafe->Informations->Address->Num."',
    '".$cafe->Informations->Address->Zip."',
    '".addslashes($cafe->Informations->Address->City)."',
    '".$cafe->Informations->Address->Longitude."',
    '".$cafe->Informations->Address->Latitude."',
    '".$site."',
    '".$tel."',
    '".$creationDate."',
    '".$userid."',
    '".$smoking."',
    '".$snack."')";
    return $sql;
  }

  function getUserIdByNameOrCreateIt($name, $pdo, $admin) {

    $user_sql = "SELECT id FROM User WHERE Name='" . $name . "'";
    $prep = $pdo->prepare($user_sql);
    $prep->execute();
    $result = $prep->fetch();
    if($result) {
      // If user commented before creating an est
      if($result["Privileges"] < $adminOrNot) {
        $user_sql = "UPDATE User SET Privileges='".$adminOrNot."' WHERE id='".$result["id"]."'";
        $prep = $pdo->prepare($user_sql);
        $prep->execute();
      }
      return $result["id"];
    }
    else {
      // Create it
      $user_sql = "INSERT INTO User (Name, MailAddress, Password, Privileges)
      VALUES('".$name."', '".$name."@mail.com', '".$name."', '".$admin."')";
      $prep = $pdo->prepare($user_sql);
      $prep->execute();
      //echo "<span class='text-success'>User successfully inserted. ID: " . $id . ", admin: ".$admin."</span><br />";

      // Get it
      $user_sql = "SELECT id FROM User WHERE Name='" . $name . "'";
      $prep = $pdo->prepare($user_sql);
      $prep->execute();
      $result = $prep->fetch();
      return $result["id"];
    }

  }

  function getTagIdByNameOrCreateIt($name, $pdo) {

    $sql = "SELECT id FROM Tag WHERE Name='" . $name . "'";
    $prep = $pdo->prepare($sql);
    $prep->execute();
    $result = $prep->fetch();
    if($result)
      return $result["id"];
    else {
      // Create it
      $sql = "INSERT INTO Tag (Name) VALUES('".$name."')";
      $prep = $pdo->prepare($sql);
      $prep->execute();
      // Get it
      return $pdo->lastInsertId();
    }

  }

  function insertComments($comments, $eid, $pdo) {

    $i = 0;
    while($comments->Comment[$i] != null) {
      $c = $comments->Comment[$i];

      $uid = getUserIdByNameOrCreateIt($c["nickname"], $pdo, 0);
      $comment_sql = "INSERT INTO Comment (Eid, Etype, Uid, Score, Text, Date)
      VALUES('".$eid."', '2', '".$uid."', '".$c["score"]."', '". addslashes($c)."', '". formatDate($c["date"]) ."')";
      $prep = $pdo->prepare($comment_sql);
      if($prep->execute()) {
        $id = $pdo->lastInsertId();
        echo "<span class='text-success'>Comment successfully inserted. ID: " . $id . "</span><br />";

      }
      else {
        echo "Error.";
        echo var_dump($prep->errorInfo());
        echo $comment_sql; //["nickname"];
        echo "<br />";
      }
      $i++;
    }

  }

  function insertTags($tags, $eid, $pdo) {

    $i = 0;
    while($tags->Tag[$i] != null) {
      $t = $tags->Tag[$i];
      $k = 0;
      while($t->User[$k] != null) {
        $u = $t->User[$k];

        // AddsTag
        $uid = getUserIdByNameOrCreateIt($u["nickname"], $pdo, 0);
        $tid = getTagIdByNameOrCreateIt($t["name"], $pdo);
        $tag_sql = "INSERT INTO AddsTag (Tid, Uid, Etype, Eid)
        VALUES('".$tid."', '".$uid."', '2', '".$eid."')";
        $prep = $pdo->prepare($tag_sql);
        if($prep->execute()) {
          $id = $pdo->lastInsertId();
          echo "<span class='text-success'>Tag successfully inserted. ID: " . $id . "</span><br />";

        }
        else {
          echo "Error.";
          echo var_dump($prep->errorInfo());
          echo $tag_sql;
          echo "<br />";
        }
        $k++;
      }
      $i++;
    }

  }

  function formatDate($d) {
    $dateElements = explode("/", $d);
    $date = new DateTime();
    $date->setDate($dateElements[2], $dateElements[1], $dateElements[0]);
    $creationDate = $date->format('Y-m-d G:i:s');
    return $creationDate;
  }

// useful: delete from Table where id > 30

  ?>
