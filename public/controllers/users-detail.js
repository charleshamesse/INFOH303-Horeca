angular.module('horeca')
.controller('UsersDetailController', function($scope, $routeParams, $http) {
  $scope.u = {};
  $scope.response = "";

  // Get user
  $http({
    method: 'GET',
    url: 'api/User.php/' + $routeParams.estId
  }).then(function successCallback(response) {
    $scope.u = angular.fromJson(response).data[0];
    $scope.success = true;
    getUserComments($scope.u.id);
    getUserTags($scope.u.id);
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });

  // Get comments
  function getUserComments(uid) {
    $http({
      method: 'GET',
      url: 'api/utilities/commentsByUser.php/' + uid
    }).then(function successCallback(response) {
      $scope.u.comments = angular.fromJson(response).data;
      $scope.success = true;
    }, function errorCallback(response) {
      $scope.response += "Comments error: " + response;
    });
  }

  // Get tags
  function getUserTags(uid) {
    $http({
      method: 'GET',
      url: 'api/utilities/tagsByUser.php/' + uid
    }).then(function successCallback(response) {
      $scope.u.tags = angular.fromJson(response).data;
      $scope.success = true;
    }, function errorCallback(response) {
      $scope.response += "Tags error: " + response;
      console.log($scope.response);
    });
  }


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
