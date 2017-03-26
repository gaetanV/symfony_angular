(function () {
    'use strict';
    angular.module('app').run(run);
    run.$inject = ['$rootScope', '$route'];
    function run($rootScope, $route) {
        $rootScope.$on("$routeChangeSuccess", function (event, current, previous) {
            if (current.redirectTo)
                return;
            $rootScope.status = 1;
        });
        $rootScope.$on("$routeChangeStart", function (event, current, previous) {
            if (current.redirectTo)
                return;
            $rootScope.status = 0;
        });
    }
})();
                    