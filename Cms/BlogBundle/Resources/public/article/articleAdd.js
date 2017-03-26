(function () {
    'use strict';
    angular
            .module('app.article')
            .controller('articleAdd', articleAdd);
    
    articleAdd.$inject = ['$scope', "articleservice"];

    function articleAdd($scope, articleservice) {

      $scope.loading = false;
        
      $scope.submit=function(e){
             e.preventDefault()
              var data=$scope.data;
              
             articleservice.add(data,function(e){
                 console.log(e);
             })
                      
        }

    }

})();
