angular.module('horeca')
.controller('ImportController', function($scope, $http) {
  $scope.test = 'Trying to import..!';
  $scope.response = "";

  // Calling the import api point
  $http({
    method: 'POST',
    url: 'api/import.php'
  }).then(function successCallback(response) {
    $scope.response = response;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });
});
