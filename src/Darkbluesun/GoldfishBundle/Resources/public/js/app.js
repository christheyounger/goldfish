var goldfishApp = angular.module('goldfishApp', ['ngRoute','goldfishServices','goldfishControllers','ngResource','xeditable']);

goldfishApp.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});

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
        templateUrl: '/bundles/darkbluesungoldfish/views/Task/detail.html',
        controller: 'TaskViewCtrl'
      }).
      when('/projects', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Project/list.html',
        controller: 'ProjectListCtrl'
      }).
      when('/projects/:projectID', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Project/detail.html',
        controller: 'ProjectViewCtrl'
      }).
      when('/clients', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Client/list.html',
        controller: 'ClientListCtrl'
      }).
      when('/clients/:clientID', {
        templateUrl: '/bundles/darkbluesungoldfish/views/Client/detail.html',
        controller: 'ClientViewCtrl'
      }).
      otherwise({
        redirectTo: '/notfound'
      });
  }]);