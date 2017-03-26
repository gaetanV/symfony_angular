(function () {
    'use strict';

    angular
            .module('app.user')
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
                .when('/article/add/', {
                    templateUrl: symfony.path('article_twig_add'),
                    controller: 'articleAdd'
                })
                .when('/article', {
                    templateUrl: symfony.path('article_twig_list'),
                    controller: 'articleList',
                })
                .when('/article/:id', {
                    templateUrl: symfony.path('article_twig_update'),
                    controller: 'articleUpdate',
                })
    }
    ;

})();