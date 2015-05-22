/*
 * directive/childrenOption.directive.js
 * This file is part of the angular directive package.
 *
 * (c) Gaetan Vigneron <gaetan@webworkshops.fr>
 *  V 0.2.0
 *  11/05/2015
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * #CONSTRUCT
 * 
 *  @target dom {select}
 *  @syntax  ckeditor {attribut}  
 *  @exemple : [ ckeditor ]
 *
 *TO DO OPTION SELECT AND BUILD
 *
 */

(function () {
    'use strict';
    angular
            .module('app')
            .directive('ckeditor', ckeditor);
    ckeditor.$inject = ['$compile'];

    function ckeditor($compile) {
        var link = function ($scope, $element, $attrs, $controller) {

            if (CKEDITOR) {
                var element = new CKEDITOR.dom.element($element[0]);
                if (element.getEditor()) {
                    var name = element.getEditor().name;
                    var instance = CKEDITOR.instances[name];
                    if (instance)
                        CKEDITOR.destroy(instance);
                }
                var ck = CKEDITOR.replace($element[0])

                ck.on('pasteState', function () {
                    $scope.$apply(function () {
                        $controller.$setViewValue(ck.getData());
                    });
                });

               /*To do on value change performance
                * ck.updateElement();
                        for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
                }
              }*/


                $controller.$render = function (value) {
                    
                    ck.setData($controller.$modelValue);
                };


            }
        }
        return {
            restrict: 'A',
            require: "ngModel",
            link: link
        };

    }
    ;

})();
