(function() {
    'use strict';
    angular
            .module('app.user')
            .controller('userAdd', UserAdd);
    UserAdd.$inject = ['$scope', "userservice"];
    
    function UserAdd($scope,userservice) { 
 
        $scope.submit=function(e){

             var formElement = angular.element(e.target);
             e.preventDefault()
              var data=$scope.data;
              
              userservice.add(data,function(e){
                   console.log(e);
              });
                  
        }
      
    }
    
})();
