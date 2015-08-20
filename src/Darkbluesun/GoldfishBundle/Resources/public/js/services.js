var goldfishServices = angular.module('goldfishServices', ['ngResource']);

goldfishServices.factory('Tasks', function($resource) {
  return $resource('/api/tasks/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});