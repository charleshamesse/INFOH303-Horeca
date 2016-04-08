angular.module('horeca')
.controller('UsersDetailController', function($scope, $routeParams, $http) {
  $scope.u = {};
  $scope.response = "";
  // Get list
  $http({
    method: 'GET',
    url: 'api/User.php/' + $routeParams.estId
  }).then(function successCallback(response) {
    $scope.u = angular.fromJson(response).data[0];
    $scope.success = true;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });


  // Delete user
  $scope.delete = function () {

    $http({
      method: 'DELETE',
      url: 'api/User.php/' + $routeParams.estId
    }).then(function successCallback(response) {
      $scope.response = angular.fromJson(response).data;
      $scope.success = true;
    }, function errorCallback(response) {
      $scope.response = "Error: " + response;
    });

  }

});
