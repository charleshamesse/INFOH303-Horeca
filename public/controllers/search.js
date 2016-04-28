angular.module('horeca')
.controller('SearchController', function($scope, $http) {
  $scope.establishments = [];
  $scope.type = "all";
  $scope.displayCreateForm = false;

  // Get list

  $scope.search = function() {
    $http({
      method: 'POST',
      data: {
        "name": $scope.name,
        "type": $scope.type,
        "tags": $scope.tags,
        "location": $scope.location
      },
      url: 'api/utilities/customSearch.php'
    }).then(function successCallback(response) {
      $scope.establishments = angular.fromJson(response).data;
      $scope.success = true;
      console.log($scope.establishments);
    }, function errorCallback(response) {
      $scope.response = "Error: " + response;
    });
  };


});
