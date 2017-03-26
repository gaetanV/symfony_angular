(function () {
    'use strict';
    angular
            .module('app.category')
            .controller('categoryUpdate', categoryUpdate);
    
    categoryUpdate.$inject = ['$scope', "$route", "categoryservice"];

    function categoryUpdate($scope, $route, categoryservice) {
        $scope.loading = true;
        $scope.data={};
        categoryservice.getOne($route.current.params.id, function (e) {
           $scope.data.CategoryUpdate={name:e.name};
    
           console.log(e);
           $scope.loading = false;
        });

        $scope.submit = function (e) {
            var formElement = angular.element(e.target);
            var formName = formElement.attr("name");

            if ($scope[formName].$valid) {
                var $data = {};
                $data[formName] = angular.copy($scope.data[formName]);
                var translations=$data[formName].name.translations;
         
               
    
                 for( var i in translations){      $data[formName].name.translations[i]= {value:translations[i].value};  }
           
     
               
                     
                categoryservice.update($route.current.params.id, $data, function (e) {
                    console.log(e);
                });
            }

            e.preventDefault();
        }

    }

})();


/* AUTO  formElement.attr("action").replace(":id", $route.current.params.id) } */