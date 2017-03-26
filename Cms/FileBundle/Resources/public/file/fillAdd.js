(function() {
    'use strict';
    angular
            .module('app.file')
            .controller('FillAdd', FillAdd);
    FillAdd.$inject = ['$scope',"fileservice"];
    
    function FillAdd($scope,fileservice) { 
   
        $scope.submit=function(e){
             e.preventDefault()
              var data=$scope.data;
              
             fileservice.add(data,function(e){
                 console.log(e);
             })
                      
        }
      
    }
    
})();


/* [files] post    var formElement = angular.element(e.target); var $data=new FormData(); var file= document.getElementById("FileCreate_file").files[0]; -*/