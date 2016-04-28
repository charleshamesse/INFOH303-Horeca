angular.module('horeca')
.controller('ExploreController', function($scope, $http) {
  $scope.establishments = [];
  $scope.searchType = "all";
  $scope.displayCreateForm = false;

  // Get list
  $http({
    method: 'GET',
    url: 'api/Establishment.php'
  }).then(function successCallback(response) {
    $scope.establishments = angular.fromJson(response).data;
    $scope.success = true;
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });

  // Create establishment
  $scope.new = {
    "Name": "",
    "Type": "Select type..",
    "Address_Street": "",
    "Address_Num": "",
    "Address_Zip": "",
    "Address_Longitude": "",
    "Address_Latitude": "",
    "Site": "",
    "Tel": "",
    "CreatedBy": $scope.Main.user.id,
    // Restaurant
    "PriceRange_LowerBound": "",
    "PriceRange_UpperBound": "",
    "Capacity": "",
    "TakeAway": false,
    "Delivery": false,
    // Bar
    "Smoking": false,
    "Snack": false,
    // Hotel
    "Stars": "0",
    "Rooms": "",
    "ExamplePrice": ""
  };
  $scope.create = function() {
    $scope.displayCreateForm = true;
  };
  $scope.post = function() {
    $http({
      method: 'POST',
      data: {"e": $scope.new},
      url: 'api/Establishment.php'
    }).then(function successCallback(response) {
      $scope.response = angular.fromJson(response).data;
      $scope.success = true;
      console.log($scope.response);
    }, function errorCallback(response) {
      $scope.response = "Error: " + response;
    });
  }

});
