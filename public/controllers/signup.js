angular.module('horeca')
.controller('SignUpController', function($scope, $routeParams, $http) {
  $scope.user = {};
  $scope.sucess = false;
  $scope.sent = false;
  $scope.APIresponse;

  $scope.randomUser = function() {
    var d = new Date(),
        n = d.getTime();

    $scope.user.Name = "DevUser" + n;
    $scope.user.MailAddress = "DevUser" + n + "@mail.com";
    $scope.user.Password = n;
  }

  $scope.submit = function() {
    $http({
      method: 'POST',
      url: 'api/User.php',
      data: $scope.user
    }).then(function successCallback(response) {
      $scope.APIresponse = angular.fromJson(response);
      $scope.success = true;
    }, function errorCallback(response) {
      $scope.APIresponse = "Error: " + response;
    });
      $scope.sent = true;
  }


});
