var goldfishControllers = angular.module('goldfishControllers', []);

goldfishControllers.controller('HomeCtrl', ['$scope','Tasks',
	function($scope,Tasks) {
		$scope.tasks = Tasks.query();
		$scope.orderProp = 'done';
		$scope.saveTask = function(task) {
			return task.$save();
		}
	}]);

goldfishControllers.controller('ClientListCtrl', ['$scope','Clients',
	function($scope,Clients) {
		$scope.clients = Clients.query();
		$scope.orderProp = 'companyName';
		$scope.saveClient = function(client) {

			return client.$save();
		}
		$scope.addClient = function() {
			$scope.inserted = new Clients();
			$scope.clients.push($scope.inserted);
		}
	}]);

goldfishControllers.controller('ClientViewCtrl', ['$scope', '$routeParams', 'Clients', 'Projects', 'Tasks', 'Users',
	 function($scope, $routeParams, Clients, Projects, Tasks, Users) {
	    $scope.client = Clients.get({id:$routeParams.clientID}, function() {
	    	$scope.projects = _.map($scope.client.projects, function(project) {
	    		return new Projects(project);
	    	});
	    	$scope.tasks = _.extend(_.map($scope.client.tasks, function(task) {
	    		task.dueDate = new Date(task.due_date);
	    		return new Tasks(task);
	    	}), {orderProp : 'dueDate'});
	    });
	    $scope.saveClient = function() {
	    	$scope.client.$save();
	    }
		$scope.addProject = function() {
			$scope.projects.push($scope.inserted = new Projects());
		}
		$scope.saveProject = function(data, id) {
			var project = new Projects(_.extend(data, {id: id, client: $scope.client}));
			return project.$save();
		}
		$scope.addTask = function() {
			$scope.tasks.push($scope.inserted = new Tasks());
		}
		$scope.saveTask = function(data, id) {
			var task = new Tasks(_.extend(data, {id: id, client: $scope.client}));
			return task.$save();
		}
		$scope.loadProjects = function() {	
			$scope.projects = Projects.query();
		}
		$scope.loadUsers = function() {
			$scope.users = Users.query();
		}
	  }]);

goldfishControllers.controller('ProjectListCtrl', ['$scope','Projects','Clients',
	function($scope,Projects,Clients) {
		Projects.query().$promise.then(function(result) {
			$scope.projects = _.map(result, function(project) {
				project.dueDate = new Date(project.due_date); return project;
			});
			$scope.clients = _.uniq(_.pluck(result, 'client'));
		});
		$scope.saveProject = function(data, id) {
			var project = new Projects(_.extend(data, {id: id}));
			if (!project.budget) return "Please specify a budget";
			if (!project.name) return "Please enter a name for the project";
			return project.$save();
		}
		$scope.addProject = function() {
			$scope.projects.push($scope.inserted = new Projects());
		}
	}]);

goldfishControllers.controller('ProjectViewCtrl', ['$scope','$routeParams','Projects','Clients','Tasks','Users',
	 function($scope, $routeParams,Projects,Clients,Tasks,Users) {
	    $scope.project = Projects.get({id:$routeParams.projectID});
	    $scope.saveProject = function(data, id) {
			var project = new Projects(_.extend(data, {id: id}));
			if (!project.budget) return "Please specify a budget";
			if (!project.name) return "Please enter a name for the project";
			return project.$save();
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
		Tasks.query().$promise.then(function(result) {
			$scope.tasks = _.map(result, function(task) {
				task.dueDate = new Date(task.due_date); return task;
			});
			$scope.projects = _.uniq(_.pluck(result, 'project'));
			$scope.clients = _.uniq(_.pluck(result, 'client'));
			$scope.users = _.uniq(_.pluck(result, 'assignee'));
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
		$scope.saveTask = function(task) {
			if (!task.name) return "task needs a name first";
			return task.$save();
		}
		$scope.addTask = function() {
			$scope.tasks.push($scope.inserted = new Tasks());
		}
	}]);

goldfishControllers.controller('TaskViewCtrl', ['$scope','$http','$routeParams','Tasks','Projects','Clients','Users',
	 function($scope, $http, $routeParams, Tasks, Projects, Clients, Users) {
	    $scope.task = Tasks.get({id:$routeParams.taskID},function() {
	    	$scope.date.dueDate = new Date($scope.task.due_date);
	    });
		$scope.saveTask = function(data, id) {
			var task = new Tasks(_.extend(data, {id: id}));
			return task.$save();
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
		$scope.addTimeEntry = function() {
			return $http.post('/api/tasks/'+$scope.task.id+'/addtime', $scope.newtimeentry).then(_.property('data')).then(function(data) {
				$scope.task.timeEntries.push(data);
			});
		}
	  }]);