angular.module('horeca')
.controller('UsersDetailController', function($scope, $routeParams, $http) {
  $scope.u = {};
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

});
