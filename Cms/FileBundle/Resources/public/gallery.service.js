(function () {
    'use strict';

    angular
            .module('app.gallery')
            .factory('galleryservice', galleryservice);

    galleryservice.$inject = ["dataservice"];
    function galleryservice(dataservice) {
        
        var route={
            prefix:"/api/gallery"
        }
             
        var service = {
            getOne: getOne,
            getAll: getAll,
            add:add,
            update:update,
            remove:remove,
        };
        
        function getOne($id,callback){  dataservice.getOne({route:route},{ url:{id:$id}}, callback);  }
        
        function getAll(callback){dataservice.getAll({route:route},{}, callback); }
        
        function add($data,callback){dataservice.add( {route:route},{param:$data}, callback); }
        
        function update($id,$data,callback){dataservice.update( {route:route},{ url:{id:$id},param:$data}, callback); }
        
        function remove($id,callback){  dataservice.remove({route:route},{ url:{id:$id}}, callback);  }
        
        return service;
           
    
    }
}
)();