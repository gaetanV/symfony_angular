(function () {
    'use strict';
    angular
            .module('app')
            .directive('initValue', InitValue);

    InitValue.$inject = ["$compile"];
    function InitValue($compile) {
        return {
            restrict: 'A',
            require: "ngModel",
            link: link
        };
        
        function link($scope, $element, $attrs, $controller) {
               $controller.$setViewValue($element[0].value);
              $controller.$render();
        }
    }
    
})();