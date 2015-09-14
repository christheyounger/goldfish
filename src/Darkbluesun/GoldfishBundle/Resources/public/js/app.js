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
      when('/projects', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Project/list.html',
        controller: 'ProjectListCtrl'
      }).
      when('/projects/:projectID', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Project/show.html',
        controller: 'ProjectViewCtrl'
      }).
      when('/clients', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Client/list.html',
        controller: 'ClientListCtrl'
      }).
      when('/clients/:clientID', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Client/show.html',
        controller: 'ClientViewCtrl'
      }).
      otherwise({
        redirectTo: '/notfound'
      });
  }]);