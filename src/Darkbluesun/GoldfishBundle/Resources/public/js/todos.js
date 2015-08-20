var goldfishApp = angular.module('goldfishApp', ['ngResource']);

goldfishApp.factory('Tasks', function($resource) {
  return $resource('/api/tasks/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});

goldfishApp.controller('TaskListController', ['$scope','Tasks',
	function($scope,Tasks) {
		$scope.tasks = Tasks.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
	}]);