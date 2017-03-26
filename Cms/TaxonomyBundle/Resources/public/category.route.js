(function () {
    'use strict';

    angular
            .module('app.user')
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
                .when('/category/add/', {
                    templateUrl: symfony.path('category_twig_list'),
                    controller: 'categoryrAdd'
                })
                .when('/category', {
                    templateUrl: symfony.path('category_twig_list'),
                    controller: 'categoryList',
                })
                .when('/category/:id', {
                    templateUrl: symfony.path('category_twig_update'),
                    controller: 'categoryUpdate',
                })
    }
    ;

})();