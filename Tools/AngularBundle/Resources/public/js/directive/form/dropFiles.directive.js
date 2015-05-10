/*
 * directive/dropeFiles.js
 * This file is part of the angular directive package.
 *
 * (c) Gaetan Vigneron <gaetan@webworkshops.fr>
 *  V 0.2.0
 *  03/05/2015
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 /**
 * @syntax  ng-drop-file 
 * @dom input
 * @require ngModel
 * @param {Json} 
 * - Option: maxsize {Integer}
 * - Option: mimeTypes {array}
 */

(function () {
    'use strict';

    angular
            .module('app')
            .directive('ngDropFile', ngDropFile);

    ngDropFile.$inject = ['$compile'];
    function ngDropFile($compile) {
        var link = function ($scope, $element, $attrs, $controller) {
            $element.addClass("drop-file-hidden");
            var multipleFile = $attrs.multiple ? true : false;
            var option={};
            if($attrs.ngDropFile){
                   option =   eval('(' + $attrs.ngDropFile + ')');
            }
          
            
            var dom_DropFile = document.createElement("DIV");
            var dom_DropCache = document.createElement("DIV");

            dom_DropCache.className = "drop-file";

            dom_DropFile.appendChild(dom_DropCache);

            if (multipleFile) {
                /*
                 * SCOPE
                 */
                var childScope = $scope.$new();
                childScope.ngDropFile = [];
                childScope.removeFile = function ($index) {
                    childScope.ngDropFile.splice($index, 1);
                    $controller.$setViewValue({tmp_file: childScope.ngDropFile});
                }

                angular.element(dom_DropFile).on('$destroy', function () {
                    childScope.$destroy();
                });

                /*
                 * DOM
                 */
                var dom_ListFile_UL, dom_ListFile_LI, dom_ListFile_IMG;
                dom_ListFile_UL = document.createElement("UL");
                dom_ListFile_UL.className = "drop-files";
                dom_ListFile_LI = document.createElement("LI");
                dom_ListFile_LI.setAttribute("ng-repeat", "file in ngDropFile");
                dom_ListFile_LI.setAttribute("ng-click", "removeFile()");

                dom_ListFile_IMG = document.createElement("IMG");
                dom_ListFile_IMG.className = "{{file.format}}";
                dom_ListFile_IMG.src = "{{file.image}}";

                dom_ListFile_LI.appendChild(dom_ListFile_IMG);
                dom_ListFile_UL.appendChild(dom_ListFile_LI);
                dom_DropFile.appendChild(dom_ListFile_UL);
                $compile(dom_DropFile)(childScope);

            }
            
  
            $element.after(dom_DropFile);

            function resetFile(){
                 if (!multipleFile) {
                        $controller.$setViewValue("");
                        dom_DropCache.style.backgroundImage="";
                   }
                    $controller.$touched=true;
            }
            
            
            
            var uploadFile = function (file) {
               
                if(option.maxsize){
                    if(file.size>option.maxsize){
                        $scope.$apply(function(){
                        
                             $controller.$setValidity('maxsize', false);
                            resetFile();
                         });   
                        return false;
                    }
                }
                 if(option.mimeTypes){
                     var valid=false;
                     for(var i=0 ;i <option.mimeTypes.length; i++){
                            
                         if(option.mimeTypes[i]==file.type){
                             valid=true;
                         }
                         
                     }
                     if(!valid){
                            $scope.$apply(function(){
                               $controller
                             $controller.$setValidity('mimeTypes', false);
                            resetFile();
                         });   
                          return false;
                     }
                }
                
                var reader = new FileReader();
                reader.onload = function (e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function () {
                        var aFile = {};
                        aFile.size = file.size;
                        aFile.image = e.target.result;
                        aFile.type = file.type;
                        aFile.name = file.name;
                        aFile.format = image.width > image.height ? "h" : "v";

                        if (multipleFile) {
                            var callback = function () {
                                childScope.ngDropFile.unshift(aFile);
                            }
                            childScope.$apply(callback);
                            $controller.$setViewValue({tmp_file: childScope.ngDropFile});
                        }
                        else {
                            dom_DropCache.style.backgroundImage = "url('" + aFile.image + "')";
                            if (aFile.format === "h") {
                                dom_DropCache.style.backgroundSize = "auto 100%";
                            } else {
                                dom_DropCache.style.backgroundSize = "100% auto";
                            }
                            $controller.$setViewValue({tmp_file: aFile});
                        }
                    };
                }
                reader.readAsDataURL(file);

            };

            var processFiles = function (filelist) {
                if (!filelist || !filelist.length)  return;
                if(option.maxsize)     $scope.$apply($controller.$setValidity('maxsize', true));
                 if(option.mimeTypes)    $scope.$apply($controller.$setValidity('mimeTypes', true));
                for (var i = 0; i < filelist.length; i++) {
                    uploadFile(filelist[i]);
                }
            };

            var block = function (e) {
                e.stopPropagation();
                e.preventDefault();
            };
            var handleDrop = function (e) {
                block(e);
                processFiles(e.dataTransfer.files);
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
