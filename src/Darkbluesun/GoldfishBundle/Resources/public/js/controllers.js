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
			$scope.inserted = new Clients();
			$scope.clients.push($scope.inserted);
		}
	}]);

goldfishControllers.controller('ClientViewCtrl', ['$scope','$routeParams','Clients',
	 function($scope, $routeParams,Clients) {
	    $scope.client = Clients.get({id:$routeParams.clientID});
	    $scope.saveClient = function() {
	    	$scope.client.$save();
	    }
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

goldfishControllers.controller('ProjectViewCtrl', ['$scope','$routeParams','Projects','Clients','Tasks','Users',
	 function($scope, $routeParams,Projects,Clients,Tasks,Users) {
	    $scope.project = Projects.get({id:$routeParams.projectID});
	    $scope.saveProject = function() {
	    	$scope.project.$save();
	    }
		$scope.loadClients = function() {
			$scope.clients = Clients.query();
		}
		$scope.loadUsers = function() {
			$scope.users = Users.query();
		}
	  }]);

goldfishControllers.controller('TaskListCtrl', ['$scope','Tasks','Projects','Clients','Users','$q',
	function($scope,Tasks,Projects,Clients,Users,$q) {
		$scope.tasks = Tasks.query(function() {
			angular.forEach($scope.tasks,function(task,key) {
				$scope.tasks[key].dueDate = new Date(task.dueDate);
			})
		});
		$scope.loadProjects = function() {	
			$scope.projects = Projects.query();
		}
		$scope.loadClients = function() {
			$scope.clients = Clients.query();
		}
		$scope.loadUsers = function() {
			$scope.users = Users.query();
		}
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			task.$save();
		}
		$scope.addTask = function() {
			$scope.tasks.push(new Tasks({editing:true}));
		}
	}]);

goldfishControllers.controller('TaskViewCtrl', ['$scope','$routeParams','Tasks','Projects','Clients','Users',
	 function($scope, $routeParams, Tasks, Projects, Clients, Users) {
	    $scope.task = Tasks.get({id:$routeParams.taskID},function() {
	    	$scope.date.dueDate = new Date($scope.task.dueDate);
	    });
	    $scope.saveTask = function() {
	    	$scope.task.$save();
	    }
		$scope.loadProjects = function() {	
			$scope.projects = Projects.query();
		}
		$scope.loadClients = function() {
			$scope.clients = Clients.query();
		}
		$scope.loadUsers = function() {
			$scope.users = Users.query();
		}
	  }]);