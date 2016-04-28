angular.module('horeca')
.controller('MainController', function($scope, $routeParams, $http, $location) {
  $scope.Main = {};
  $scope.Main.user = {
    "Privileges": "None"
  };

  $scope.Main.refreshLogin = function() {

    $http({
      method: 'GET',
      url: 'api/utilities/privileges.php'
    }).then(function successCallback(response) {
      $scope.Main.user = angular.fromJson(response).data;
      $scope.Main.adminMode = ($scope.Main.user.Privileges == 'Admin');
    }, function errorCallback(response) {
      $scope.Main.response = "Error: " + response;
    });
  };

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
      $scope.Main.adminMode = ($scope.Main.user.Privileges == 'Admin');
    }, function errorCallback(response) {
      $scope.Main.response = "Error: " + response;
    });
  };

  $scope.Main.refreshLogin();

  // Custom search
  $scope.customSearch = function () {
    $location.path("/search/" + $scope.Main.customSearch);
  }

});
