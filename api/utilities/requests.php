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
    <div class="row">
      <div class="col-sm-12">
        <h1>.brussels requests</h1>
        <!--

        R1

        -->
        <h3>R1: Tous les utilisateurs qui apprécient au moins 3 établissements que Brenda apprécie</h3>
        <pre><?php
        $R1_userName = 'Brenda';
        $R1_allEst = "
        (SELECT E.Name, E.id, E.CreatedBy, 0 as EType
        FROM Restaurant E)
        UNION
        (SELECT E.Name, E.id, E.CreatedBy, 1 as EType
        FROM Hotel E)
        UNION
        (SELECT E.Name, E.id, E.CreatedBy, 2 as EType
        FROM Bar E)";
        $R1_estBrendaLikes = "
        SELECT E.Name, E.id, E.EType
        FROM (".$R1_allEst.") E, Comment C, User U
        WHERE E.Etype = C.Etype
        AND E.id = C.Eid
        AND C.Score >=4
        AND U.Name = '".$R1_userName."'
        AND C.Uid = U.id
        GROUP BY E.Name
        ";

        $R1_usersWhoLike = "
        SELECT U2.Name
        FROM User U2, Comment C2, (" . $R1_estBrendaLikes . ") E2
        WHERE C2.Uid = U2.id
        AND C2.Eid = E2.id
        AND C2.Etype = E2.EType
        AND C2.Score >= 4
        GROUP BY U2.Name
        HAVING COUNT(E2.Name) >= 3";

        $R1 = $R1_usersWhoLike; //usersWhoLike;//$estBrendaLikes;//$usersWhoLike;
        $prep = $pdo->prepare($R1);
        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        var_dump($results);

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
        SELECT *
        FROM User U2
        WHERE NOT EXISTS
          ( SELECT E2.Name
          FROM (" . $R2_estBrendaLikes . ") E2
          WHERE NOT EXISTS
            ( SELECT *
            FROM Comment C2
            WHERE C2.Eid = E2.id
            AND C2.Etype = E2.EType
            AND C2.Score >= 4
            AND C2.Uid = U2.id) )
        ";
        $R2_allEst = $R1_allEst;

        $R2_estLikedByThem = "
        SELECT E3.Name
        FROM (".$R2_allEst.") E3, (".$R2_usersWhoLikeSameEstAsBrenda.") U3, Comment C3
        WHERE C3.Score >= 4
        AND C3.Uid = U3.id
        AND C3.Eid = E3.id
        AND C3.Etype = E3.EType
        GROUP BY E3.id
        ";
        $R2 = $R2_estLikedByThem; //usersWhoLike;//$estBrendaLikes;//$usersWhoLike;
        $prep = $pdo->prepare($R2);
        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          var_dump($prep->errorInfo());
        }
        ?></pre>
        Attention: peut-être enlever ceux que Brenda apprécie..
        <!--

        R3

        -->
        <h3>R3: Tous les établissements pour lesquels il y a au plus un commentaire</h3>
        <pre><?php
        $R3 = 'Brenda';
        $R3_allEst = $R2_allEst;

        $R3_maxOneComment = "
        SELECT E.Name
        FROM (".$R3_allEst.") E, Comment C
        WHERE C.Eid = E.id AND C.Etype = E.EType
        GROUP BY E.Name
        HAVING COUNT(C.id) <= 1";

        $R3_bis = "SELECT E.Name, E.id
        FROM (".$R3_allEst.") E
        WHERE NOT EXISTS (
          SELECT E.id
          FROM Comment C
          WHERE C.Eid = E.id AND C.Etype = E.EType
          HAVING COUNT(C.id) > 1
        )";

        $R3 = $R3_bis;//$R3_maxOneComment;
        $prep = $pdo->prepare($R3);
        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }
        ?></pre>
        <h3>R4: La liste des administrateurs n'ayant pas commenté tous les établissements qu'ils ont crées</h3>
        <pre><?php
        $R4_allEst = $R2_allEst;

        $R4_adminsWhoCommentedAll = "
        SELECT U.id
        FROM User U
        WHERE Privileges=1
        AND NOT EXISTS
          ( SELECT E.Name
          FROM (" . $R4_allEst. ") E
          WHERE E.CreatedBy = U.id
          AND NOT EXISTS
            ( SELECT *
            FROM Comment C
            WHERE C.Eid = E.id
            AND C.Etype = E.EType
            AND C.Uid = U.id) )";

        $R4_allAdmins = "
        SELECT *
        FROM User U
        WHERE U.Privileges = 1
        AND U.id NOT IN (".$R4_adminsWhoCommentedAll.")
        ";

        $R4 = $R4_allAdmins;
        $prep = $pdo->prepare($R4);
        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }
        ?></pre>
        <h3>R5: La liste des établissements ayant au minimum trois commentaires, classée selon la moyenne des scores attribués</h3>
        <pre><?php

        $R5_allEst = $R2_allEst;

        $R5 = "SELECT E.Name, AVG(C.Score) as CS
        FROM (".$R5_allEst.") E, Comment C
        WHERE C.Etype = E.EType AND C.Eid = E.id
        GROUP BY E.id, E.EType
        HAVING COUNT(C.id) >= 3
        ORDER BY CS DESC";

        $prep = $pdo->prepare($R5);

        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }

        ?></pre>
        <h3>R6: La liste des labels étant appliqués à au moins 5 établissements, classée selon la moyenne des scores des établissements ayant ce label</h3>
        <pre><?php
        $R6_allEst = $R2_allEst;

        $R6_scores = "SELECT E.Name, E.id, E.EType, AVG(C.Score) as CS
        FROM (".$R5_allEst.") E, Comment C
        WHERE C.Etype = E.EType AND C.Eid = E.id
        GROUP BY E.id, E.EType";

        $R6_tags = "SELECT T.Name, T.id, COUNT(DISTINCT A.Eid) AS Count
        FROM AddsTag A, Tag T
        WHERE A.Tid = T.id
        GROUP BY A.Tid
        HAVING COUNT(DISTINCT A.Eid) >= 5";

        $R6_addsTagGroupedByEid = "SELECT * FROM AddsTag GROUP BY Tid, Eid, Etype";

        $R6 = "SELECT T.Name, AVG(S.CS), T.Count as EstablishmentsWithThisTag
        FROM (".$R6_tags.") T
        JOIN (".$R6_addsTagGroupedByEid.") A ON T.id = A.Tid
        JOIN (".$R6_scores.") S ON A.Eid = S.id AND A.Etype = S.EType
        GROUP BY T.id
        ORDER BY AVG(S.CS) DESC";

        $prep = $pdo->prepare($R6);

        $prep->execute();
        $results = $prep->fetchAll(PDO::FETCH_ASSOC);
        echo var_dump($results);

        if($prep->errorInfo()[1] != null) {
          echo "error:";
          echo var_dump($prep->errorInfo());
        }

        ?></pre>

      </div>
    </div>
  </div>
</body>
</html>
