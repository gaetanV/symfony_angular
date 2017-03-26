(function () {
    'use strict';

    angular
            .module('app.file')
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
                .when('/file', {
                    templateUrl: symfony.path('file_twig_list'),
                    controller: 'fileList'
                })
    }
    ;

})();