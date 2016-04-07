angular.module('horeca')
.controller('ImportController', function($scope, $http) {
  $scope.input = '';
  $scope.response = "";
  $scope.isPosted = false;
  $scope.success;
  // Calling the import api point
  $scope.post = function() {
    $http({
      method: 'POST',
      url: 'api/importFromXML.php',
      data: {
        "xml": $scope.input,
        "author": "unknown"
      }
    }).then(function successCallback(response) {
      $scope.response = response;
      $scope.success = true;
      $scope.isPosted = true;
    }, function errorCallback(response) {
      $scope.response = "Error: " + response;
      $scope.isPosted = true;
    });
  };

  $scope.copyExample = function() {
    $scope.input = $scope.example;
  };
  $scope.example = `<Restaurant creationDate="2/10/2008" nickname="fred">
  <Information>
    <Name>Grenier d’Elvire</Name>
    <Address>
      <Street> Chaussée de Boondael</Street>
      <Num>339A</Num>
      <Zip> 1050 </Zip>
      <City>Ixelles</City>
      <Longitude>4.38384</Longitude>
      <Latitude>50.818766</Latitude>
    </Address>
    <Site link="http://www2.resto.be/grenierdelvire/"/>
    <Tel>02/648 43 48</Tel>
    <Closed>
      <!-- am = fermé le midi, pm = fermé le soir -->
      <!-- day O = lundi, ..., day 6 = dimanche -->
      <On day="0" hour="am"/>
      <On day="1" hour="am"/>
      <On day="6" hour="am"/>
      </Closed>
    <TakeAway/>
    <Delivery/>
    <PriceRange>20</PriceRange>
    <Banquet capacity="25"/>
  </Information>
  <Comments>
    <Comment nickname="boris" date="2/10/2008" score="4">texte</Comment>
    ...
  </Comments>
  <Tags>
    <Tag name="Bon rapport qualité/prix">
      <User nickname ="fred"/>
      ...
    </Tag>
    ...
  </Tags>
</Restaurant>`;
});
