angular.module('horeca')
.controller('UsersController', function($scope, $http) {
  $scope.users = [];
  // Get list
  $http({
    method: 'GET',
    url: 'api/User.php'
  }).then(function successCallback(response) {
    $scope.users = angular.fromJson(response).data;
    $scope.success = true;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });

});
