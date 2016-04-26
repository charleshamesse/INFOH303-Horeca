var app = angular.module('horeca', ['ngRoute', 'ngSanitize', 'ngMap'])
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
      when('/explore/:estType/:estId', {
        templateUrl: 'public/views/explore-detail.html',
        controller: 'ExploreDetailController'
      }).
      when('/users', {
        templateUrl: 'public/views/users.html',
        controller: 'UsersController'
      }).
      when('/users/:estId', {
        templateUrl: 'public/views/users-detail.html',
        controller: 'UsersDetailController'
      }).
      when('/signup', {
        templateUrl: 'public/views/signup.html',
        controller: 'SignUpController'
      }).
      when('/signin', {
        templateUrl: 'public/views/signin.html',
        controller: 'SignInController'
      }).
      otherwise({
        redirectTo: '/home'
      });
  }]);
