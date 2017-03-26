(function () {
    'use strict';
    angular
            .module('app.category')
            .controller('categoryList', categoryList);
    categoryList.$inject = ['$scope', "categoryservice"];

    function categoryList($scope, categoryservice) {

        $scope.loading = true;
        categoryservice.getAll(function (e) {
            $scope.loading = false;
            $scope.list = e;
    

        });
        
         $scope.submit=function(e){
       
             e.preventDefault()
              var data=$scope.data;
         
             categoryservice.add(data,function(e){
                 console.log(e);
             })
                      
        }
   
            $scope.isNotFirstDepth=function($dragScope){
                return $dragScope.$depth>1;
            }
        
        $scope.isNotChild=function($transport,item){
              var  flag=true;
               if($transport.children)   if($transport.children.indexOf(item)!=-1)   return false;

             if($transport==item)return false;
              function inCollection(array){
                 
                  if(array.indexOf($transport)!=-1){  flag=false;}
                  for(var i=0; i<array.length; i++){
                      if(array[i].children)    inCollection(array[i].children);
                   }
               
            }
             if(item.children)  inCollection(item.children);
             return flag;

        }
        
        $scope.push=function ($transport,$item,$dragScope) {
                categoryservice.update($transport.id, {CategoryUpdateParent:{parent:null}} ,function(e){

                        $dragScope.$collection.splice($dragScope.$index,1);   
                        $item.unshift($transport);   
                    });
        };

        $scope.pushChildren = function ($transport,$item,$dragScope) {
            categoryservice.update($transport.id, {CategoryUpdateParent:{parent:$item.id}} ,function(e){
                $dragScope.$collection.splice($dragScope.$index,1);   
                if(!$item.children){ $item.children=new Array(); }
                $item.children.unshift($transport);  
            });
        }
        
   
    }

})();
