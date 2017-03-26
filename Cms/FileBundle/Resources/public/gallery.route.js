(function () {
    'use strict';

    angular
            .module('app.gallery')
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
                .when('/gallery', {
                    templateUrl: symfony.path('category_twig_list'),
                    controller: 'galleryList',
                })
                .when('/gallery/:id', {
                    templateUrl: symfony.path('category_twig_update'),
                    controller: 'galleryUpdate',
                })
    }
    ;

})();