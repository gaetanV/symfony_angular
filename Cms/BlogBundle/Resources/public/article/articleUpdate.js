(function () {
    'use strict';
    angular
            .module('app.article')
            .controller('articleUpdate', articleUpdate);
    
    articleUpdate.$inject = ['$scope', "$route", "articleservice","categoryservice","galleryservice"];

    function articleUpdate($scope, $route ,articleservice,categoryservice,galleryservice) {
                $scope.loading = true;
               
                $scope.data={};
                 categoryservice.getAll(function (e) {
      
                        $scope.list = {ArticleUpdate:{category:e}};
                        articleservice.getOne($route.current.params.id, function (e) {
                  
                       
                      
                                         $scope.data.ArticleUpdate.title=e.title;
                                            $scope.data.ArticleUpdate.description=e.description;
                                             $scope.data.ArticleUpdate.content=e.content;
                                                $scope.data.ArticleUpdate.category=e.category;
                                                
                                                
                                                 $route.current.params.gallery="92";
                                                galleryservice.getOne(92, function (e) {

                                              
                                                     $route.current.gallery=e.files;
                                           

                                                });
                                                     
                                                  
                                         console.log(e);
                                         $scope.loading = false;

                                            var domCollection=document.getElementsByClassName("ckeditor");
                                        //  console.log(domCollection);
                                
                                              for(var i=0; i<domCollection.length; i++){
                                                 
                                                
                                                  //  var element = new CKEDITOR.dom.element(domCollection[i]);
                                           
                                                    //  if(element.getEditor()) console.log(element.getEditor().name);
                                       
                                     
                                               }
                                         
                                      });

                });
                
              
                
  $scope.submit = function (e) {
            var formElement = angular.element(e.target);
            var formName = formElement.attr("name");

            if ($scope[formName].$valid) {
                var $data = {};
                $data[formName] = angular.copy($scope.data[formName]);
                
                var translations=$data[formName].title.translations;
                 for( var i in translations){      $data[formName].title.translations[i]= {value:translations[i].value};  }
                 
                  var translations=$data[formName].description.translations;
                 for( var i in translations){      $data[formName].description.translations[i]= {value:translations[i].value};  }
     
                var translations=$data[formName].content.translations;
                 for( var i in translations){      $data[formName].content.translations[i]= {value:translations[i].value};  }
                     
                articleservice.update($route.current.params.id, $data, function (e) {
                    console.log(e);
                });
            }

            e.preventDefault();
        }
    }
    
    
    

})();
