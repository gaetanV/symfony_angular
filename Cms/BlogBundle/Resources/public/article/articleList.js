(function () {
    'use strict';
    angular
            .module('app.article')
            .controller('articleList', articleList);
    
    articleList.$inject = ['$scope', "articleservice"];

    function articleList($scope, articleservice) {

        $scope.loading = true;
        articleservice.getAll(function (e) {
            $scope.loading = false;
             $scope.list = e;
        });

        $scope.remove = function ($index, item) {
            articleservice.remove(item.id, function (e) {
                $scope.list.splice($index, 1);
            });
        }
        
        
      $scope.submit=function(e){
             e.preventDefault()
              var data=$scope.data;
              
             articleservice.add(data,function(e){
                 console.log(e);
             })
                      
        }

    }

})();
