/*
 * directive/childrenRepeat.directive.js
 * This file is part of the angular directive package.
 *
 * (c) Gaetan Vigneron <gaetan@webworkshops.fr>
 *  V 0.3.0
 *  11/05/2015
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * #CONSTRUCT
 * 
 *  @target dom 
 *  @syntax children-repeat {attribut}  
 *      Require: 
 *          repeat {string} 
 *          in {string} 
 *          track by {string}
 *  @exemple : [  children-repeat = "item in sample track by children" ]
 *
 *
  * ## DOM ( Child ) : Options 
 *  @syntax depth {attribut}  
 *      :depth {integer} | end {string} 
 *  @exemple : [ depth = "1" ,  depth = "end"   ]
*   If exist use this node else use first node
 *   
 *   ## DOM ( Child ) : Inject Variables
  *  @scope 
  *     \children-repeat\repeat {object} 
 *      $index {integer} 
 *      $depth {integer} 
 *      $collection {object} (object parent)
 *    @exemple : [  {{item.title}} , {{$index}} , {{$depth}}  , {{$collection.children}}  , {{$collection.title}}]
 *      
 * # RESULT 
 * 
 * ## DOM ( Child )
 * @class  depth{{ :depth | integer }}  
 * @exemple : class="depth1"
 *              
 *
 */

(function () {
    'use strict';

    angular
            .module('app')
            .directive('childrenRepeat', ChildrenRepeat);

    function ChildrenRepeat() {
        return {
            transclude: true,
            compile: compile,
        };
        function compile($element, $attrs, transclude) {
            /**
             * @Define options
             */
            var option, indexName, collectionName, scopeChildName;

            try {
                    if(!$attrs.childrenRepeat) throw "you should specified children-repeat attribute";
                    var match = $attrs.childrenRepeat.match(/^\s*(.+)\s+in\s+(.*?)\s*track\s+by\s+(.+)\s*?$/);  
                    indexName =match[1];
                    collectionName =match[2];
                    scopeChildName =  match[3];
                    if(!indexName || !collectionName || !scopeChildName) throw "you should specified all attributes for children-repeat attribute";
            } catch (e) {
                /**
                 * @Error  : Syntax or Parameter Required missing
                 */
                console.log(e);
                return;
            }

            return {pre: link};

            function link($scope, element) {

                var nodeParent = element;
                var nodeParentName = nodeParent[0].nodeName;

                /**
                 * @Observe  scope.collectionName on change
                 */
                $scope.$watch(collectionName, function (collection) {
                    /**
                     * @Constraint  collection ?  replace dom  ( nodeParent.children ) with new collection
                     */
                    if (collection) {
                
                        nodeParent.children().remove();
                        buildListNode(collection, nodeParent, 0);
                    }
                    ;

                    /**
                     * @parm collection {array}
                     * @parm parent {angular element}
                     * @parm depth  {integer} 
                     * @recursion collection [:depth]
                     * Create dom with scope of each index of collection
                     */

                    function buildListNode(collection, parent, depth) {

                        /**
                         * @Constraint  depth !=0 build container depth else use container depth as parent
                         */
                        if (depth !== 0) {
                            var container = angular.element(document.createElement(nodeParentName));
                            container.addClass("depth" + depth);
                            angular.element(parent).append(container);
                            parent = container;
                        }
                        depth++;

                        /**
                         * @Define  childScope {scope}
                         */
                        for (var i = 0; i < collection.length; i++) {

                            var childScope = $scope.$new();
                            childScope[indexName] = collection[i];
                            childScope["$index"]= i;
                            childScope["$depth"]=depth;
                            childScope["$collection"]=collection;
                          
                            /**       
                             *@param childscope {scope}
                             *@callback 
                             * - clone {collection type angular element}
                             * Select node model and append html element to container depth
                             */
                            transclude(childScope, function (clone) {

                                /** 
                                 * @Define  haveChild
                                 */
                                var haveChild = collection[i][scopeChildName] && collection[i][scopeChildName].length > 0 ? true : false;

                                /** 
                                 * @Constraint  model  ? use this node  : use first node 
                                 */
                                var model = 0;
                                for (var j = 0; j < clone.length; j++) {

                                    if (clone[j] instanceof HTMLElement) {
                                        if (model == 0)
                                            model = j;
                                        if (!haveChild) {
                                            if (clone[j].getAttribute("depth") == "end") {
                                                var custom = j;
                                                break;

                                            }
                                        }
                                        if (clone[j].getAttribute("depth")) {
                                            if (clone[j].getAttribute("depth") == depth - 1) {
                                                var custom = j;
                                                break;
                                            }
                                        }
                                        ;
                                    }
                                }
                                var id = custom ? custom : model;
                                parent.append(clone[id]);
    
                                /**
                                 * @Observe clone on destroy : destroy childScope
                                 */
                                clone.on('$destroy', function () {
                                    childScope.$destroy();
                                });

                                /**
                                 * @Constraint  haveChild ? recursion
                                 */
                                if (haveChild) {
                                    buildListNode(collection[i][scopeChildName], clone[id], depth);
                                }
                            });
                        }
                        ;
                    }
                    ;

                }, true);
            }
            ;
        }

    }
    ;


})();
