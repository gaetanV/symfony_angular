(function () {
    'use strict';

    angular
            .module('app.user')
            .factory('userservice', userservice);

    userservice.$inject = ["dataservice"];
    function userservice(dataservice) {
        
        var route={
            prefix:"/api/user",
            login: {
                path: "/login_check",
                method: "POST"
            },
        }
             
        var service = {
            getOne: getOne,
            getAll: getAll,
            add:add,
            update:update,
            remove:remove,
            login:login
        };
        
        function login($data,callback){
             dataservice.post("login",{route:route},{param:$data},callback);
        }
        
        function getAll(callback){dataservice.getAll({route:route},{}, callback); }
        
        function getOne($id,callback){  dataservice.getOne({route:route},{ url:{id:$id}}, callback);  }
        
        function getAll(callback){dataservice.getAll({route:route},{}, callback); }
        
        function add($data,callback){dataservice.add( {route:route},{param:$data}, callback); }
        
        function update($id,$data,callback){dataservice.update( {route:route},{ url:{id:$id},param:$data}, callback); }
        
        function remove($id,callback){  dataservice.remove({route:route},{ url:{id:$id}}, callback);  }
        
        return service;
           
    }
}
)();

  /*to do for cache
        var table={
            name:"user",
            index:["id"],
            constraintTable:[],  //on destroy
            cache:200
        }*/