/**
* (c) Gaetan Vigneron <gaetan@webworkshops.fr>
*  Generate by angularBundle V {{version}}
*  @Module: {{name}}
*  @Build: {{date}}  
*/

(function () {
    'use strict';

    angular
            .module("{{namespace}}")
            .config(route);

    route.$inject = ['$routeProvider'];

    function route($routeProvider) {
        $routeProvider
        {% for route in routes %}
                .when("{{route['path']}}", {
                    templateUrl: '{{route["defaults"]["angular"]["templateUrl"]}}',
                    controller: '{{route["defaults"]["angular"]["controller"]}}'
                })
        {% endfor %}
    }
    ;

})();