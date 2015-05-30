/**
* (c) Gaetan Vigneron <gaetan@webworkshops.fr>
*  @GenerateBy angular symfony  : https://github.com/gaetanV/symfony_angular
*  @Module: {{namespace}} 
*  @Version :  {{version}}
*/
(function() {
    'use strict';
     angular.module('{{namespace}}', []);
    
    angular
            .module("{{namespace}}")
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
        {% for route in routes %}
                .when("{{route['path']}}", {
                    templateUrl: '{{route["templateUrl"]}}',
                    controller: '{{route["controller"]}}'
                })
        {% endfor %}
    }
    ;

})();