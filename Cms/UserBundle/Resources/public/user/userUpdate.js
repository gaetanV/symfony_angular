(function () {
    'use strict';
    angular
            .module('app.user')
            .controller('userUpdate', userUpdate);
    
    userUpdate.$inject = ['$scope', "$route", "userservice"];

    function userUpdate($scope, $route, userservice) {
        $scope.loading = true;

        userservice.getOne($route.current.params.id, function (e) {
            $scope.loading = false;
            $scope.data.UserUpdate.username = e.username;
        });

        $scope.submit = function (e) {
            var formElement = angular.element(e.target);
            var formName = formElement.attr("name");
            
            if ($scope[formName].$valid) {
                var $data = {};
                $data[formName] = $scope.data[formName];

                userservice.update($route.current.params.id, $data, function (e) {
                    console.log(e);
                });
            }

            e.preventDefault();
        }

    }

})();


/* AUTO  formElement.attr("action").replace(":id", $route.current.params.id) }  $route option route */