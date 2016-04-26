angular.module('horeca')
.controller('ExploreDetailController', function($scope, $routeParams, $http) {
  $scope.est = {};

  // Get establishment
  $http({
    method: 'GET',
    url: 'api/Establishment.php/' + $routeParams.estType + '/' + $routeParams.estId
  }).then(function successCallback(response) {
    $scope.est = angular.fromJson(response).data[0];
    $scope.success = true;

    // Fetch tags
    $http({
      method: 'GET',
      url: 'api/utilities/tagsByEstablishment.php/' + $routeParams.estType + '/' + $routeParams.estId
    }).then(function successCallback(response) {
      $scope.est.Tags = angular.fromJson(response).data;
      $scope.success = true;
    }, function errorCallback(response) {
      $scope.response += "Tags error: " + response;
      $scope.success = false;
    });


    // Fetch half days off if it is a restaurant
    if($scope.est.Type == 0) {
      $http({
        method: 'GET',
        url: 'api/utilities/halfDaysOff.php/' + $routeParams.estId
      }).then(function successCallback(response) {
        $scope.est.HalfDaysOff = angular.fromJson(response).data;
        $scope.success = true;
      }, function errorCallback(response) {
        $scope.response += "Days off error: " + response;
        $scope.success = false;
      });
    }
  }, function errorCallback(response) {
    $scope.response = "Error: " + response;
  });


  // Get comments
  $http({
    method: 'GET',
    url: 'api/utilities/commentsByEstablishment.php/' + $routeParams.estType + '/' + $routeParams.estId
  }).then(function successCallback(response) {
    $scope.reviews = angular.fromJson(response).data['comments'];
    $scope.est.Rating = angular.fromJson(response).data['rating']['AvgScore'];
  }, function errorCallback(response) {
    $scope.reviewsResponse = "Error: " + response;
  });

  // Get tags
  var getTags = function() {
  $http({
    method: 'GET',
    url: 'api/Tag.php'
  }).then(function successCallback(response) {
    $scope.tags = angular.fromJson(response).data;
  }, function errorCallback(response) {
    $scope.response += "Tags error: " + response;
  });
};
getTags();

  // Write comments
  $scope.displayCommentForm = false;
  $scope.review = {
    "Score": 3,
    "text": ""
  };
  $scope.sent = false;
  $scope.APIresponse = "";
  $scope.success;
  $scope.writeReview = function() {
    $scope.displayReviewForm = true;
  };
  $scope.updateScore = function(s) {
    $scope.review.Score = s;
  }
  $scope.sendReview = function() {
    $http({
      method: 'POST',
      url: 'api/Comment.php',
      data: {
        "Uid": $scope.Main.user.id,
        "Eid": $scope.est.id,
        "Etype": $scope.est.Type,
        "Score": $scope.review.Score,
        "Text": $scope.review.Text
      }
    }).then(function successCallback(response) {
      $scope.APIresponse = angular.fromJson(response);
      $scope.success = true;
      $scope.sent = true;
    }, function errorCallback(response) {
      $scope.APIresponse = "Error: " + response;
      $scope.sent = true;
    });
  };

  // Add tags
  $scope.addTag = function() {
    console.log($scope.newTag);
    $http({
      method: 'POST',
      url: 'api/Tag.php',
      data: {
        "Uid": $scope.Main.user.id,
        "Eid": $scope.est.id,
        "Etype": $scope.est.Type,
        "Tag": $scope.newTag,
        }
      }).then(function successCallback(response) {
        $scope.APIresponse = angular.fromJson(response);
        console.log($scope.APIresponse);
        $scope.success = true;
        $scope.sent = true;
        getTags();
      }, function errorCallback(response) {
        $scope.APIresponse = "Error: " + response;
        $scope.sent = true;
      });
  }

});
