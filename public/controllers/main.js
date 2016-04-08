angular.module('horeca')
.controller('MainController', function($scope, $routeParams, $http) {
  $scope.Main = {};
  $scope.Main.user = {
    "Privileges": "None"
  };

  $http({
    method: 'GET',
    url: 'api/utilities/privileges.php'
  }).then(function successCallback(response) {
    $scope.Main.user = angular.fromJson(response).data;
    console.log($scope.Main.user);
  }, function errorCallback(response) {
    $scope.Main.response = "Error: " + response;
  });

  $scope.Main.logout = function() {
    $http({
      method: 'POST',
      url: 'api/utilities/privileges.php',
      data: {
        'action': 'logout'
      }
    }).then(function successCallback(response) {
      $scope.Main.user = {
        "Privileges": "None"
      };
    }, function errorCallback(response) {
      $scope.Main.response = "Error: " + response;
    });
  };

});
