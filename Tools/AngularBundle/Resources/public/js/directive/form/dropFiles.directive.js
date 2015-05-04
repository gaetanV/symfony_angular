(function () {
    'use strict';

    angular
            .module('app')
            .directive('ngDropFile', ngDropFile);

   ngDropFile.$inject = ['$compile'];
    function ngDropFile($compile) {
           var link = function ($scope, $element, $attrs, $controller) {
     
            $element.addClass("drop-file-hidden");
            

            var ngDropFile = $scope.ngDropFile = [];
       
          
           
              
            $scope.removeFile = function ($index) {
                ngDropFile.splice($index, 1);
            }

            var newList = $compile("<ul class=\"drop-files clearfix \" ><li ng-click=\"removeFile()\" ng-repeat='file in ngDropFile' > <input   type=\"hidden\" value=\"{{file}}\" name=\"" + $attrs.name + "[]\"> <img class=\"{{file.format}}\" ng-src=\"{{file.image}}\" />  </li></ul>")($scope);
            var dropCache = angular.element("<div class=\"drop-file\"></div>");
            $element.after(newList);
            $element.after(dropCache);

            var uploadFile = function (file) {

                var reader = new FileReader();
                reader.onload = function (e) {
                    console.log(e);
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function () {
                        var aFile = {};
                        aFile.size = file.size;
                        aFile.image = e.target.result;
                        aFile.name = file.name;
                        aFile.type=file.type;
                        aFile.format = image.width > image.height ? "h" : "v";
                        var callback = function () {
                                 $controller.$setViewValue({tmp_file:aFile});
                                  ngDropFile.unshift(aFile);
                        }

                        $scope.$apply(callback);


                    };

                }
                reader.readAsDataURL(file);

            };

            var processFiles = function (filelist) {
                if (!filelist || !filelist.length)
                    return;
                for (var i = 0; i < filelist.length; i++) {
                    uploadFile(filelist[i]);
                }

            };

            var handleDrop = function (e) {
                block(e);
                processFiles(e.dataTransfer.files);
            };
            var block = function (e) {
                e.stopPropagation();
                e.preventDefault();
            };
            var handleChange = function (e) {
                processFiles(e.currentTarget.files);
            };
         
            document.addEventListener('drop', block, false);
            document.addEventListener('dragover', block, false);


            $element.on('drop', handleDrop);
            $element.on('change', handleChange);

        }
        return {
            restrict: 'A',
             require: "ngModel",
            link: link,
        };
    }
    ;


})();
