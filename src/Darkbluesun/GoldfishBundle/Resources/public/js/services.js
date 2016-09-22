var goldfishServices = angular.module('goldfishServices', ['ngResource']);

goldfishServices.factory('Tasks', function($resource) {
  return $resource('/api/tasks/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});

goldfishServices.factory('Projects', function($resource) {
  return $resource('/api/projects/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    },
    query: {
      transformResponse: function(data) {
        return _.extend({}, _.map(angular.fromJson(data), function(project) {
          project.due_date = project.due_date ? new Date(project.due_date) : null;
          return project;
        }));
      }
    },
    get: {
      transformResponse: function(data) {
        data = angular.fromJson(data);
        data.due_date = data.due_date ? new Date(data.due_date) : null;
        return data;
      }
    }
  });
});

goldfishServices.factory('Clients', function($resource) {
  return $resource('/api/clients/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});

goldfishServices.factory('Users', function($resource) {
  return $resource('/api/users/:id', { id: '@id' }, {
    update: {
      method: 'PUT'
    }
  });
});