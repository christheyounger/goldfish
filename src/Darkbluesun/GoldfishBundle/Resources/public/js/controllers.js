var goldfishControllers = angular.module('goldfishControllers', []);

goldfishControllers.controller('HomeCtrl', ['$scope','Tasks',
	function($scope,Tasks) {
		$scope.tasks = Tasks.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
	}]);

goldfishControllers.controller('TaskListCtrl', ['$scope','Tasks',
	function($scope,Tasks) {
		$scope.tasks = Tasks.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
	}]);

goldfishControllers.controller('TaskViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.taskID = $routeParams.taskID;
	  }]);