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
 *  @syntax  children-option {attribut}  
 *      Require: 
 *          repeat {string} 
 *          in {string} 
 *          track by {string}
 *  @exemple : [  children-option = "item in sample track by children" ]
 *
 *TO DO OPTION SELECT AND BUILD
 *
 */

(function () {
    'use strict';
    angular
            .module('app')
            .directive('childrenOption', childrenOption);
    childrenOption.$inject = ['$compile'];

    function childrenOption($compile) {
        var link = function ($scope, $element, $attrs, $controller) {

            /**
             * @Error  target type
             */
            if ($element[0].nodeName !== "SELECT") {
                throw "Node must be of type SELECT";
                return;
            }
            var multiple = $attrs.multiple ? true : false;


            /**
             * @Define options
             */
            var indexName, collectionName, scopeChildName, indexText, collection;
            try {
                if (!$attrs.childrenOption)
                    throw "you should specified children-option attribute";
                var match = $attrs.childrenOption.match(/^\s*(.+)\s+as\s*(.+)\s+in\s+(.*?)\s*track\s+by\s+(.+)\s*?$/);
                indexName = match[1];
                indexText = match[2];
                collectionName = match[3];
                scopeChildName = match[4];
                if (!indexName || !collectionName || !scopeChildName || !indexText)
                    throw "you should specified all attributes for children-repeat attribute";
            } catch (e) {
                /**
                 * @Error  : Syntax or Parameter Required missing
                 */
                console.log(e);
                return;
            }



            var findDom, inputDom, ulDom, liDom, liDivDom;

            /**
             * @Define childscope
             */
            var childScope = $scope.$new();

            var initScope = function (collection) {


                collection = collection;
                childScope.curentModel = "";
                childScope.searchText = {};
                childScope.searchText.text = childScope.curentModel;

                childScope.finderFields = collection;

                function checkChildren(children) {
                    for (var i = 0; i < children.length; i++) {

                        for (var j = 0; j < currentValue.length; j++) {

                            if (currentValue[j] == children[i][indexName]) {

                                childScope.selectFields.push(children[i]);

                            }

                        }
                        if (children[i][scopeChildName])
                            checkChildren(children[i][scopeChildName]);

                    }
                }
                /**
                 * @Constraint  option multiple
                 */
                if (multiple) {

                    childScope.selectFields = new Array();


                    $controller.$formatters.push(function (ngmodel) {

                        currentValue = ngmodel;
                        checkChildren(childScope.finderFields);

                    });

                    var currentValue = $controller.$modelValue ? $controller.$modelValue : new Array();
                    checkChildren(childScope.finderFields);

                    /**
                     * @On  remove a field
                     */
                    childScope.removeField = function (select, $index) {
                        childScope.selectFields.splice($index, 1);
                        var currentValue = $controller.$modelValue ? $controller.$modelValue : new Array();
                        currentValue.splice(currentValue.indexOf(select.value), 1);

                        //  childScope.finderFields.push(select);
                        $controller.$setViewValue(currentValue);
                    }
                }

                /**
                 * @On  select a field
                 */
                childScope.selectField = function (select, $index) {
                    if (select[indexName] != null) {
                        /**
                         * @Constraint  option multiple
                         */

                        if (multiple) {

                            var currentValue = $controller.$modelValue ? $controller.$modelValue : new Array();
                            if (currentValue.indexOf(select[indexName]) === -1) {
                               
                                if (childScope.selectFields.indexOf(select) === -1) {
                                    currentValue.push(String(select[indexName]));
                                    childScope.selectFields.push(select);
                                }
                            }


                            //   childScope.finderFields.splice(childScope.finderFields.indexOf(select), 1);



                            $controller.$setViewValue(currentValue);
                        } else {

                            $controller.$setViewValue(String(select[indexName]));
                        }

                        childScope.curentModel = select[indexText];

                        inputDom.value = childScope.curentModel;
                        ulDom.style.display = "none";

                        $controller.$render();
                    }
                };

            }

            /**
             * @Build dom
             */
            var initDom = function () {
                $element.css('display', 'none');   // Hide
                findDom = document.createElement("div");
                inputDom = document.createElement("input");

                inputDom.setAttribute("ng-model", "searchText.text");
                /**
                 * @Constraint  option multiple
                 */
                if (multiple) {
                    ulDom = document.createElement("ul");
                    liDom = document.createElement("li");
                    liDom.setAttribute("ng-repeat", "selectField in selectFields");
                    liDom.setAttribute("ng-click", "removeField(selectField,$index)");
                    var node = document.createTextNode("{{selectField." + indexText + "}}");
                    liDom.appendChild(node);
                    ulDom.appendChild(liDom);
                    findDom.appendChild(ulDom);
                }

                ulDom = document.createElement("ul");
                ulDom.style.display = "none";

                ulDom.setAttribute("children-repeat", "finderField in finderFields track by " + scopeChildName + "");
                liDom = document.createElement("li");

                liDivDom = document.createElement("div");
                liDivDom.setAttribute("ng-click", "selectField(finderField,$index)");
                var node = document.createTextNode("{{finderField." + indexText + "}}");
                liDivDom.appendChild(node);

                liDom.appendChild(liDivDom);
                ulDom.appendChild(liDom);
                findDom.appendChild(inputDom);
                findDom.appendChild(ulDom);



                $compile(findDom)(childScope);

                $element.after(findDom);
                angular.element(findDom).on('$destroy', function () {
                    childScope.$destroy();
                });

                var hide = function (e) {
                    e.stopPropagation();

                    if (e.target == inputDom)
                        return false;

                    var parent = e.target;

                    while ((parent = parent.parentNode) != null) {

                        if (parent == ulDom)
                            return false;

                    }

                    /*
                     while( ( parent=e.target.parentNode)!="undefined"){
                     if(parent== ulDom)  return false;
                     }
                     
                     */
                    ulDom.style.display = "none";
                    inputDom.value = childScope.curentModel;
                    document.removeEventListener('mousedown', hide);
                }

                var show = function (e) {
                    ulDom.style.display = "block";
                    /**
                     * @Observe dom document mousedown > hide()
                     */
                    document.addEventListener('mousedown', hide);
                    e.stopPropagation();
                }

                /**
                 * @Observe dom input click > show()
                 */
                inputDom.addEventListener('click', show, false);
            }


            $scope.$watch(collectionName, function (collection) {

                if (collection) {

                    $element.next().remove();
                    initScope(collection);
                    initDom();
                }
                ;
            });


        }
        return {
            restrict: 'A',
            require: "ngModel",
            link: link
        };

    }
    ;

})();
