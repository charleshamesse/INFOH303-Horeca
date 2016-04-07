var app = angular.module('horeca', ['ngRoute', 'ngSanitize'])
.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/home', {
        templateUrl: 'public/views/home.html',
        controller: 'HomeController'
      }).
      when('/import', {
        templateUrl: 'public/views/import.html',
        controller: 'ImportController'
      }).
      when('/phones/:phoneId', { // Kept as example for passing parameters
        templateUrl: 'partials/phone-detail.html',
        controller: 'PhoneDetailCtrl'
      }).
      otherwise({
        redirectTo: '/home'
      });
  }]);
