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
  <style type="text/css">
  pre, div{
    overflow:visible;
    white-space:pre-line;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h1>.brussels requests</h1>
        <!--

        R1

        -->
        <h3>R1: Tous les utilisateurs qui apprécient au moins 3 établissements que Brenda apprécie</h3>
        <pre><?php
        $R1_userName = 'Brenda';
        $R1_estBrendaLikes = "
        (SELECT E.Name, E.id, 0 as EType
        FROM Restaurant E
        RIGHT JOIN Comment C ON C.Eid = E.id AND C.Etype = 0
        RIGHT JOIN User U ON C.Uid = U.id
        WHERE U.Name = '".$R1_userName."'
        AND C.Score >= 3
        AND E.Name IS NOT NULL)
        UNION
        (SELECT E.Name, E.id, 1 as EType
        FROM Hotel E
        RIGHT JOIN Comment C ON C.Eid = E.id AND C.Etype = 1
        RIGHT JOIN User U ON C.Uid = U.id
        WHERE U.Name = '".$R1_userName."'
        AND C.Score >= 3
        AND E.Name IS NOT NULL)
        UNION
        (SELECT E.Name, E.id, 2 as EType
        FROM Bar E
        RIGHT JOIN Comment C ON C.Eid = E.id AND C.Etype = 2
        RIGHT JOIN User U ON C.Uid = U.id
        WHERE U.Name = '".$R1_userName."'
        AND C.Score >= 3
        AND E.Name IS NOT NULL)";

        $R1_usersWhoLike = "
        SELECT U2.Name
        FROM User U2, Comment C2, (" . $R1_estBrendaLikes . ") E2
        WHERE C2.Uid = U2.id
        AND C2.Eid = E2.id
        AND C2.Etype = E2.EType
        AND C2.Score >= 3
        GROUP BY U2.Name
        HAVING COUNT(E2.Name) >= 3";

        $R1 = $R1_usersWhoLike; //usersWhoLike;//$estBrendaLikes;//$usersWhoLike;
        $prep = $pdo->prepare($R1);
        $prep->execute();
        $results = $prep->fetchAll();
        echo json_encode($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }
        ?></pre>
        <!--

        R2

        -->
        <h3>R2: Tous les établissements qu'apprécie au moins un utilisateur qui apprécie tous les établissements que Brenda apprécie</h3>
        <pre><?php
        $R2_estBrendaLikes = $R1_estBrendaLikes;

        $R2_usersWhoLikeSameEstAsBrenda = "
        SELECT U2.Name, U2.id
        FROM User U2, Comment C2, (" . $R1_estBrendaLikes . ") E2
        WHERE C2.Uid = U2.id
        AND C2.Eid = E2.id
        AND C2.Etype = E2.EType
        AND C2.Score >= 3
        GROUP BY U2.Name
        HAVING COUNT(E2.Name) >= 3";

        $R1 = $R1_usersWhoLike; //usersWhoLike;//$estBrendaLikes;//$usersWhoLike;
        $prep = $pdo->prepare($R1);
        $prep->execute();
        $results = $prep->fetchAll();
        echo json_encode($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }
        ?></pre>
        <!--

        R3

        -->
        <h3>R3: Tous les établissements pour lesquels il y a au plus un commentaire</h3>
        <pre><?php
        $R3 = 'Brenda';
        $R3_allEst = "
        (SELECT E.Name, E.id, 0 as EType
        FROM Restaurant E)
        UNION
        (SELECT E.Name, E.id, 1 as EType
        FROM Hotel E)
        UNION
        (SELECT E.Name, E.id, 2 as EType
        FROM Bar E)";

        $R3_maxOneComment = "
        SELECT E.Name
        FROM (".$R3_allEst.") E, Comment C
        WHERE C.Eid = E.id AND C.Etype = E.EType
        GROUP BY E.Name
        HAVING COUNT(C.Score) <= 1";

        $R3 = $R3_maxOneComment;
        $prep = $pdo->prepare($R3);
        $prep->execute();
        $results = $prep->fetchAll();
        echo json_encode($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }
        ?></pre>
        <h3>R4: La liste des administrateurs n'ayant pas commenté tous les établissements qu'ils ont crées</h3>
        <pre></pre>
        <h3>R5: La liste des établissements ayant au minimum trois commentaires, classée selon la moyenne des scores attribués</h3>
        <pre></pre>
        <h3>R6: La liste des labels étant appliqués à au moins 5 établissements, classée selon la moyenne des scores des établissements ayant ce label</h3>
        <pre></pre>

      </div>
    </div>
  </div>
</body>
</html>