var todoApp = angular.module('todoApp', ['ngResource']);

todoApp.factory('Todos', function($resource) {
  return $resource('/app/todos/api/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});

todoApp.controller('TodoListController', ['$scope','Todos',
	function($scope,Todos) {
		$scope.todos = Todos.query();
		$scope.orderProp = 'done';
		$scope.saveTodo = function(todo) {
			todo.$save();
		}
	}]);