(function () {
    'use strict';
    angular
            .module('app')
            .controller('galleryList', galleryList);
    galleryList.$inject = ['$scope','$location',"galleryservice"];

    function galleryList($scope, $location,galleryservice) {

        $scope.loading = true;
        galleryservice.getAll(function (e) {
            $scope.loading = false;
            $scope.list = e;
        });

        $scope.remove = function ($index, item) {
            galleryservice.remove(item.id, function (e) {
                $scope.list.splice($index, 1);
            });
        }

        $scope.submit = function (e) {
            e.preventDefault()
            var data = $scope.data;
            galleryservice.add(data, function (e) {
                console.log(e);
                $location.path('/gallery/' + e.result.id);
                $location.replace();
            })

        }

    }

})();
