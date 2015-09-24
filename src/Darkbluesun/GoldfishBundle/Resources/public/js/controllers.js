var goldfishControllers = angular.module('goldfishControllers', []);

goldfishControllers.controller('HomeCtrl', ['$scope','Tasks',
	function($scope,Tasks) {
		$scope.tasks = Tasks.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
	}]);

goldfishControllers.controller('ClientListCtrl', ['$scope','Clients',
	function($scope,Clients) {
		$scope.clients = Clients.query();
		$scope.orderProp = 'companyName';
		$scope.saveClient = function(client) {
			client.$save();
		}
		$scope.addClient = function() {
			$scope.clients.push(new Clients({edit:true}));
		}
	}]);

goldfishControllers.controller('ClientViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.clientID = $routeParams.clientID;
	  }]);

goldfishControllers.controller('ProjectListCtrl', ['$scope','Projects',
	function($scope,Projects) {
		$scope.projects = Projects.query();
		$scope.orderProp = 'done';
		$scope.saveProject = function(project) {
			project.$save();
		}
	}]);

goldfishControllers.controller('ProjectViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.projectID = $routeParams.projectID;
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