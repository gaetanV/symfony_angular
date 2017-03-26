(function () {
    'use strict';
    angular
            .module('app.gallery')
            .controller('galleryUpdate', galleryUpdate);
    
    galleryUpdate .$inject = ['$scope', "$route", "galleryservice","fileservice"];

    function galleryUpdate ($scope, $route, galleryservice,fileservice) {
        var id=$route.current.params.gallery?$route.current.params.gallery:$route.current.params.id;
    
        $scope.loading = true;
        if(!$route.current.gallery){
                galleryservice.getOne(id, function (e) {

                    $scope.data.GalleryUpdate={name:e.name};

                    $scope.files=e.files;
                    $scope.loading = false;

                });
            }else{
                  $scope.files=$route.current.gallery;
                     $scope.loading = false;
            }
        
       $scope.remove = function ($index, item) {
            fileservice.remove(item.id, function (e) {
                $scope.files.splice($index, 1);
            });
        }
        
        $scope.submit = function (e) {
            var formElement = angular.element(e.target);
            var formName = formElement.attr("name");
            
            if ($scope[formName].$valid) {
                var $data = {};
                $data[formName] = $scope.data[formName];
                
                    galleryservice.update(id, $data, function (e) {
                        if(e.result){
                          $scope.files.push(e.result);
                      }
                          console.log(e);
                });
            }

            e.preventDefault();
        }

    }

})();


/* AUTO  formElement.attr("action").replace(":id", $route.current.params.id) } */