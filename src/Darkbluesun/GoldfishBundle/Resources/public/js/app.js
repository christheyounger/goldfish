var goldfishApp = angular.module('goldfishApp', ['ngRoute','goldfishServices','goldfishControllers','ngResource']);

goldfishApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/', {
        templateUrl: '/bundles/darkbluesungoldfish/views/home.html',
        controller: 'HomeCtrl'
      }).
      when('/notfound', {
        templateUrl: '/bundles/darkbluesungoldfish/views/notfound.html',
        controller: 'HomeCtrl'
      }).
      when('/tasks', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Task/list.html',
        controller: 'TaskListCtrl'
      }).
      when('/tasks/:taskID', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Task/show.html',
        controller: 'TaskViewCtrl'
      }).
      otherwise({
        redirectTo: '/notfound'
      });
  }]);