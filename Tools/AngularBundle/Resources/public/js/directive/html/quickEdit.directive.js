(function () {
    'use strict';

    angular
            .module('app')
            .directive('quickEdit', QuickEdit);

    QuickEdit.$inject = ["$compile"];
    function QuickEdit($compile) {

        return {
            transclude: true,
            compile: compile,
        };

        function compile($element, $attr, transclude) {
            
            return {pre: link};
            function link($scope, element) {
                $scope.quickModel = $attr.quickEdit;
                $scope.switchModel = function (id) {
                    $scope.quickModel = id;
                };
                $scope.$watch("quickModel", function (model) {
         
                    transclude($scope, function (clone) {
                        
                        
                        for (var i = 0; i < clone.length; i++) {
                            if(clone[i].toString()=="[object HTMLDivElement]")
                               var attModel = clone[i].getAttribute("quick-model");
                     
                            if (attModel=== model) {
                                element.children().remove();
                                var attAction = clone[i].getAttribute("quick-action");
                                if (attAction) {
                                    $scope[attAction]($scope);
                                }
                                ;

                                element.append(clone[i]);
                                break;
                             }  
                        }

                    });
                });


            }
            ;
        }
        ;

    }

})();
