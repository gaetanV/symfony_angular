(function () {
    'use strict';
    angular
            .module('app.user')
            .controller('userList', userList);
    
    userList.$inject = ['$scope', "userservice"];

    function userList($scope, userservice) {
        $scope.loading = true;
        userservice.getAll(function (e) {
            $scope.loading = false;
            $scope.list = e;
        });

        $scope.remove = function ($index, item) {
            userservice.remove(item.id, function (e) {
                $scope.list.splice($index, 1);
            });
        }
    }
})();
