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
      when('/explore', {
        templateUrl: 'public/views/explore.html',
        controller: 'ExploreController'
      }).
      when('/explore/:estId', {
        templateUrl: 'public/views/explore-detail.html',
        controller: 'ExploreDetailController'
      }).
      otherwise({
        redirectTo: '/home'
      });
  }]);
