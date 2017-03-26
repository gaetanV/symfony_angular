(function () {
    'use strict';

    angular
            .module('app.user')
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
                .when('/user/add/', {
                    templateUrl: symfony.path('cms_admin')+"user/add/",
                    controller: 'UserAdd'
                })
                .when('/user', {
                    templateUrl: symfony.path('cms_admin')+"user",
                    controller: 'userList',
                })
                .when('/user/:id', {
                    templateUrl: symfony.path('cms_admin')+"user/1",
                    controller: 'userUpdate',
                })
    }
    ;

})();