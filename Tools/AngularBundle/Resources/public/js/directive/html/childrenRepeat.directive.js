(function() {
    'use strict';

    angular
            .module('app')
            .directive('childrenRepeat', ChildrenRepeat);


    function ChildrenRepeat() {
        return {
            transclude: true,
            compile: compile  ,
         };  
         function compile($element, $attr,transclude) {
                    //ATTRIBUTS
                    var myLoop = $attr.childrenRepeat;
                    var match = myLoop.match(/^\s*(.+)\s+in\s+(.*?)\s*(\s+track\s+by\s+(.+)\s*)?$/);   /// A FAIRE TRACK BY
                    var indexName = match[1];
                    var collectionName = match[2];
                    var scopeChildName = match[4];
                    
   
                    return { pre:link};

                    function link($scope,element){       
                        //NODE PARENT
                        var nodeParent = element;
                        var nodeParentName=nodeParent[0].nodeName;
                    
                        //ON SCOPE "collectionString" CHANGE
                        $scope.$watch(collectionName, function(collection) {
                           if(collection){
                               nodeParent.children().remove();
                               buildListNode(collection, nodeParent, 0);
                             };
                            function buildListNode(collection, parent, nv) {
                          
                                // If is not First Level , Build a list container
                                if (nv !== 0) {
                                    var container = angular.element(document.createElement(nodeParentName));
                                    container.addClass("lv" + nv);
                        
                                     angular.element(parent).append(container);
                                    parent = container;
                                }
                                nv++;

                                for (var i = 0; i < collection.length; i++) {
                                    // Build a scope for each children
                                    var childScope = $scope.$new();
                                    childScope[indexName] = collection[i];
                                    childScope["$"+indexName] = {
                                        id: i,
                                        level: nv,
                                        parent: collection
                                    };

                                    //Build a item dom for each children ( transclude with new scope )
                                    transclude(childScope, function(clone) {
                                           var haveChild= collection[i][scopeChildName]?true :false;
                                           var model=0;
                                           for (var j = 0; j < clone.length; j++) {
                                              
                                                if(clone[j] instanceof HTMLElement){
                                                           if(model==0)  model=j; 
                                                           if(!haveChild){
                                                                 if(clone[j].getAttribute("lv")=="end"){
                                                                     var custom=j;  
                                                                     break; 
                                                                    
                                                                 }
                                                           }
                                                          if ( clone[j].getAttribute("lv")) {
                                                                       if(clone[j].getAttribute("lv")==nv-1){
                                                                             var custom=j;  
                                                                             break;
                                                                       }  
                                                           };
                                                }
                                            }

                                            var id=  custom? custom : model;
                                        
                                            parent.append(clone[id]);
                                        
                                     
                                    
                                        //Clean scope on destroy (nodeParent.children().remove());
                                        clone.on('$destroy', function() {
                                            childScope.$destroy();
                                        });

                                        // If have children (scope.scopeChildName) loop on buildListNode
                                        if (haveChild) {
                                                 buildListNode(collection[i][scopeChildName], clone[id], nv);
                                        }
                                    });
                                }
                                ;
                            }
                            ;
                          
                        }, true);
                    };
                }
      
    }
    ;


})();
