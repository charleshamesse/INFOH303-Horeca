angular.module('horeca')
.controller('ExploreDetailController', function($scope, $routeParams, $http) {
  $scope.est = {};
  // Get list
  $http({
    method: 'GET',
    url: 'api/Restaurant.php/' + $routeParams.estId
  }).then(function successCallback(response) {
    $scope.est = angular.fromJson(response).data[0];
    $scope.success = true;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });

});
