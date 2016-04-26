angular.module('horeca')
.controller('SignInController', function($scope, $routeParams, $http) {
  $scope.user = {};
  $scope.success = false;
  $scope.sent = false;
  $scope.APIresponse;


  $scope.submit = function() {
    $http({
      method: 'POST',
      url: 'api/utilities/signin.php',
      data: $scope.user
    }).then(function successCallback(response) {
      $scope.APIresponse = angular.fromJson(response);
      console.log($scope.APIresponse.data);
      if($scope.APIresponse.data == "\"Success\"") {
        $scope.success = true;
        $scope.Main.refreshLogin();
      }
      else if($scope.APIresponse.data == "\"Incorrect password\"") {
        $scope.success = false;
      }
      else {
        $scope.success = false;
      }
        $scope.sent = true;
    }, function errorCallback(response) {
      $scope.APIresponse = "Error: " + response;
        $scope.sent = true;
    });
  };


});
