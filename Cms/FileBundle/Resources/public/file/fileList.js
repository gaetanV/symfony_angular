(function () {
    'use strict';
    angular
            .module('app.file')
            .controller('fileList', fileList);
    fileList.$inject = ['$scope', "fileservice"];

    function fileList($scope, fileservice) {

        $scope.loading = true;
        fileservice.getAll(function (e) {
            $scope.loading = false;
            $scope.list = e;
        });

        $scope.remove = function ($index, item) {
            fileservice.remove(item.id, function (e) {
                $scope.list.splice($index, 1);
            });
        }

    }

})();
