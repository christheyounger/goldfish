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
			$scope.clients.push(new Clients({editing:true}));
		}
	}]);

goldfishControllers.controller('ClientViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.clientID = $routeParams.clientID;
	  }]);

goldfishControllers.controller('ProjectListCtrl', ['$scope','Projects','Clients',
	function($scope,Projects,Clients) {
		$scope.projects = Projects.query();
		$scope.clients = Clients.query();
		$scope.orderProp = 'done';
		$scope.saveProject = function(project) {
			project.$save();
		}
		$scope.addProject = function() {
			$scope.projects.push(new Projects({editing:true}));
		}
	}]);

goldfishControllers.controller('ProjectViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.projectID = $routeParams.projectID;
	  }]);

goldfishControllers.controller('TaskListCtrl', ['$scope','Tasks','Projects','Clients','Users','$q',
	function($scope,Tasks,Projects,Clients,Users,$q) {
		$scope.tasks = Tasks.query();		
		$scope.projects = Projects.query();
		$scope.clients = Clients.query();
		$scope.users = Users.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
		$scope.addTask = function() {
			$scope.tasks.push(new Tasks({editing:true}));
		}
	}]);

goldfishControllers.controller('TaskViewCtrl', ['$scope','$routeParams',
	 function($scope, $routeParams) {
	    $scope.taskID = $routeParams.taskID;
	  }]);