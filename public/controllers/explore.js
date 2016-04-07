angular.module('horeca')
.controller('ExploreController', function($scope, $http) {
  $scope.establishments = [];
  $scope.searchType = "all";
  // Get list
  $http({
    method: 'GET',
    url: 'api/Restaurant.php'
  }).then(function successCallback(response) {
    $scope.establishments = angular.fromJson(response).data;
    console.log($scope.establishments);
    $scope.success = true;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });

});
