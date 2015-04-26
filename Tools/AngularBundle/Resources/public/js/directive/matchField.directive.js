(function() {
    'use strict';
    angular
            .module('app')
            .directive('matchField', MatchField);

    function MatchField() {
         return {
             require: '?ngModel',
              link:link
         };  
         function link(scope, elem, attrs, ctrl){
                
              var field = attrs.matchField;
              var match;
           
              if (!ctrl) return;
              
              ctrl.$validators.match=function(modelValue, viewValue){

                       return modelValue===match || false;
              }
           
              scope.$watch(field,
              function(val){
                  
                   match=val;   
                   ctrl.$validate();
                }
               );
         }; 
    };
    
})();
